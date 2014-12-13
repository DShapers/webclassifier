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

    public function __construct(InputInterface $input, OutputInterface $output)
    {
        parent::__construct();
        $this->output = $output;
        $this->input  = $input;
        $this->domCrawler = new DomCrawler();
    }

    public function handleDocumentInfo(PHPCrawlerDocumentInfo $pageInfo)
    {
        $this->domCrawler->clear();
        $this->domCrawler->addContent($pageInfo->content);
        $this->output->writeln("<info>Indexing ".$pageInfo->url."</info>");
        $content = implode('', $this->domCrawler->filterXPath('//text()[not(ancestor::script)]')->extract('_text'));
    }
}
