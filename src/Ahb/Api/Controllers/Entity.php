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
}
