<?php
namespace Ahb\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Yaml\Parser as YamlParser;


class ClassifyCommand extends Command
{

    protected function configure()
    {
        $this->setName('crawler:classify')->setDescription("Classify crawled document with entities");
    }

    protected function execute(InputInterface $in, OutputInterface $out)
    {
        $esClient = new \Elasticsearch\Client(array('hosts'=>array('localhost:9200')));
        $em       = \Ahb\DoctrineBootstrap::getEntityManager();
        $searchP  = array('index'=>'openinov', 'type'=>'document');
        $entities = $em->getRepository('Ahb\Entities\Entity')->findAll();
        foreach ($entities as $entity) {
            $body            = array('query'=>array('multi_match'=>array('query'=>$entity->keywords,'fields'=>array('url^5','title^4','content'),'operator'=>'and')), 'fields'=>array('id','url','title'));
            $searchP['body'] = $body;
            $matchingDocs    = $esClient->search($searchP);
            $this->classify($out, $esClient, $em, $entity, $matchingDocs);
        }
        $em->flush();
    }

    protected function classify($out, $esClient,$em, $entity, $docs)
    {
        if (!$docs['hits'] && !$docs['hits']['max_score']) {
            return false;
        }
        $min_score = 0.8*$docs['hits']['max_score'];
        $docCount  = 0;
        $searchP   = array('index'=>'openinov','type'=>'document');
        foreach ($docs['hits']['hits'] as $hit) {
            if ($hit['_score']<=$min_score) continue;
            $searchP['body']['doc'] = array('entities'=>array('id'=>$entity->id,'name'=>$entity->name,'score'=>$hit['_score']));
            $searchP['id'] = $hit['_id'];
            try {
                $esClient->update($searchP);
                $docCount++;
            } catch (\Exception $e) {
                $out->writeln('<error>Cannot update document in ES : '.$e->getMessage().'</error>');
            }
        }
        $out->writeln('<info>Entity '.$entity->name.' has been classified in '.$docCount.' documents</info>');
        $entity->updateDate = time();
        $entity->documentNumber += $docCount;
        $em->persist($entity);
    }

}

