<?php
namespace Ahb\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Yaml\Parser as YamlParser;

use Ahb\Crawlers\NewsWebsiteCrawler;

class CrawlCommand extends Command
{
    protected function configure()
    {
        $this->setName("crawl")->setDescription("Crawl all websites defined in seed list");
        $this->addOption("config", null, InputOption::VALUE_OPTIONAL);
        $this->addOption("pagelimit", null, InputOption::VALUE_OPTIONAL);
        $this->addOption("process", null, InputOption::VALUE_OPTIONAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $config    = $this->getConfig($input);
            $urlSeeder = $this->getUrlSeeder($config);
            $urls      = $urlSeeder->getUrls();
            $pageLimit = $input->getOption("pagelimit") ?: 100;
            $process   = $input->getOption("process") ?: 5;
            foreach ($urls as $url) {
                $output->writeln("<info>******************************************</info>");
                $output->writeln("<info>Start indexing ".$url['url']."</info>");
                $output->writeln("<info>******************************************</info>");
                $crawler = new NewsWebsiteCrawler($input, $output);
                $crawler->setCrawlInfo(array('source'=>$url['source']));
                $crawler->setURL($url['url']);
                $crawler->addContentTypeReceiveRule("#text/html#");
                $crawler->setPageLimit($pageLimit);
                $crawler->addURLFilterRule($url['filterRules']);
                $crawler->goMultiProcessed($process, 2);
                $crawler = null;
            }
       } catch (\Exception $e) {
            $output->writeln("<error>".(string)$e."</error>");
        }
    }

    private function getConfig(InputInterface $input)
    {
        $configFile = $input->getOption('config');
        if ($configFile) {
            if (!file_exists($configFile)) {
                $output->writeln('<warning>'.$configFile.' does not exist, taking default config file ahbcrawl.yml</warning>');
                $configFile = "./config/ahbcrawl.yml";
            }
        } else {
            $configFile = "./config/ahbcrawl.yml";
        }
        $yaml   = new YamlParser();
        $config = $yaml->parse(@file_get_contents($configFile));
        if (!$config) {
            throw new \Exception('No configuration file found');
        }
        return $config;
    }

    private function getUrlSeeder($config)
    {
        $urlSeeder = $config['url_seeder'];
        $className = $urlSeeder['class'];
        $params    = $urlSeeder['parameters'];
        $seeder    = null;
        if (class_exists($className)) {
            $seeder = new $className($params);
        } else {
            throw new \Exception('Url seeder '.$className.' cannot be loaded');
        }
        return $seeder;
    }
}
