<?php
namespace Ahb\Api\Controllers;

use Symfony\Component\HttpFoundation\JsonResponse;

class Entity
{

    public function __construct($app)
    {
        $this->app = $app;
    }

    public function get()
    {
        $em = \Ahb\DoctrineBootstrap::getEntityManager()->getRepository('Ahb\Entities\Entity');
        return $this->app->json($em->findAll());
    }

    public function post()
    {
        $em = \Ahb\DoctrineBootstrap::getEntityManager();
        $entity = new \Ahb\Entities\Entity();
        $entity->name           = $this->app['request']->request->get('name');
        $entity->keywords       = $this->app['request']->request->get('keywords');
        $entity->documentNumber = 0;
        $entity->updateDate     = time();
        $em->persist($entity);
        $em->flush();
        return new JsonResponse("Entity created",201);
    }

    public function whatsNew()
    {
        $esClient = new \Elasticsearch\Client(array('hosts'=>array('127.0.0.1:9200')));
        $searchP  = array('index'=>'openinov', 'type'=>'document');
        $query    = 
                  array (
                    'query' => 
                    array (
                      'term' => 
                      array (
                        'readDate' => 0,
                      ),
                    ),
                    'size' => 100
                  );
        $searchP['body'] = json_encode($query, true);
        $results         = $esClient->search($searchP);
        $return          = array();
        foreach($results['hits']['hits'] as $article) {
            if (!$article['_source']['entities']) {
                continue;
            }
            $entity   = $article['_source']['entities']['name'];
            $entityId = $article['_source']['entities']['id'];
            $article['_source']['score']       = round($article['_source']['entities']['score'], 2);
            $article['_source']['crawledDate'] = date("Y-m-d", $article['_source']['crawledDate']);
            unset($article['_source']['content']);
            unset($article['_source']['entities']);
            if (!isset($return[$entity])) {
                $return[$entity] = array('entityName'=>$entity, 'entityId'=>$entityId, 'articles'=>array());
            }
            $return[$entity]['articles'][] = $article['_source'];
        }
        return new JsonResponse($return, 200);
    }

}
