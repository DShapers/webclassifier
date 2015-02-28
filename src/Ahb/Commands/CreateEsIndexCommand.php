<?php
namespace Ahb\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateEsIndexCommand extends Command
{
    protected function configure()
    {
        $this->setName("crawler:index:create")->setDescription("Create the ES index that contains documents");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $esClient = new \Elasticsearch\Client(array("hosts"=>array("127.0.0.1:9200")));
        $params['index'] = 'openinov';
        $params['body']['settings']['number_of_shards']   = 1;
        $params['body']['settings']['number_of_replicas'] = 1;
        $params['body']['mappings']['document'] = array(
            '_source'=>array('enable'=>true),
            'properties' => array(
                'title' => array(
                    "type"   => "multi_field",
                    "fields" => array(
                        "title" => array("type"=>"string", "index"=>"analyzed"),
                        "untouched" => array("type"=>"string", "index"=>"not_analyzed")
                    )
                ),
                'url' => array(
                    "type"   => "multi_field",
                    "fields" => array(
                        "url" => array("type"=>"string", "index"=>"analyzed"),
                        "untouched" => array("type"=>"string", "index"=>"not_analyzed")
                      )
                 ),
                 'entities' => array(
                    'type'=>'nested',
                    'properties'=> array(
                        'id'=>array('type'=>'integer'),
                        'score'=>array('type'=>'float'),
                        'name'=>array('type'=>'string','index'=>'not_analyzed')
                    )
                )
            )
        );
        try {
            $esClient->indices()->create($params);
        } catch (\Exception $e) {
            $output->writeln("<error>Impossible to create index ".$e->getMessage()."</error>");
        }
    }
}
