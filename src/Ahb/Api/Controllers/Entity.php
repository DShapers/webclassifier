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
  'size' => 0,
  'aggs' => 
  array (
    'entities' => 
    array (
      'nested' => 
      array (
        'path' => 'entities',
      ),
      'aggs' => 
      array (
        'entity' => 
        array (
          'terms' => 
          array (
            'field' => 'entities.name',
          ),
          'aggs' => 
          array (
            'title_per_entity' => 
            array (
              'reverse_nested' => new \StdClass(), 
              'aggs' => 
              array (
                'title' => 
                array (
                  'terms' => 
                  array (
                    'field' => 'title.untouched',
                  ),
                  'aggs' => 
                  array (
                    'url' => 
                    array (
                      'terms' => 
                      array (
                        'field' => 'url.untouched',
                      ),
                    ),
                  ),
                ),
              ),
            ),
          ),
        ),
      ),
    ),
  ),
);
        $searchP['body'] = json_encode($query, true);
        $results         = $esClient->search($searchP);
        $return          = array();
        foreach($results['aggregations']['entities']['entity']['buckets'] as $entity) {
            $return[$entity['key']] = array();
            foreach($entity['title_per_entity']['title']['buckets'] as $item) {
                $return[$entity['key']][] = array('title'=>$item['key'], 'url'=>$item['url']['buckets'][0]['key']);
            }
        }
        return new JsonResponse($return, 200);
    }

}
