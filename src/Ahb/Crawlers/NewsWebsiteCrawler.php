<?php
namespace Ahb\Crawlers;

use PHPCrawler\PHPCrawler;
use PHPCrawler\PHPCrawlerDocumentInfo;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DomCrawler\Crawler as DomCrawler;

class NewsWebsiteCrawler extends PHPCrawler
{
    private $input;
    private $output;
    private $domCrawler;
    private $crawlInfo;

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        parent::__construct();
        $this->output     = $output;
        $this->input      = $input;
        $this->domCrawler = new DomCrawler();
    }

    public function setCrawlInfo($crawlInfo)
    {
        $this->crawlInfo = $crawlInfo;
    }

    public function handleDocumentInfo(PHPCrawlerDocumentInfo $pageInfo)
    {
        if ($pageInfo->http_status_code !== 200) {
            return;
        }
        $this->domCrawler->clear();
        $this->domCrawler->addContent($pageInfo->content);
        $this->output->writeln("<info>Indexing ".$pageInfo->url."</info>");
        $content          = implode('', $this->domCrawler->filterXPath('//text()[not(ancestor::script)]')->extract('_text'));
        $doc              = new \Ahb\Entities\Document;
        $doc->content     = $content;
        $doc->crawlerId   = $this->getCrawlerId();
        $doc->source      = $this->crawlInfo['source'];
        $doc->url         = $pageInfo->url;
        try {
            $doc->title       = $this->domCrawler->filterXPath('//title')->text();
            $doc->documentHash= $this->buildDocumentHash($doc->url);
            $doc->crawledDate = time();
            $doc->size = $pageInfo->bytes_received;
            $em = \Ahb\DoctrineBootstrap::getEntityManager();
            $em->persist($doc);
            $em->flush();
         } catch (\Exception $e) {
            $this->output->writeln("<error>Forget article : "+$e->getMessage()+"</error>");
            return;
        }
    }

    protected function buildDocumentHash($url)
    {
        $urlParsed = parse_url($url);
        if (!$urlParsed) {
            return null;
        }
        $path = isset($urlParsed['path']) ? $urlParsed['path'] : '';
        $hash = $urlParsed['host']."/".$path;
        return $hash;
    }
}
