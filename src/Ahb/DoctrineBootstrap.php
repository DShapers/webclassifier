<?php

namespace Ahb;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

class DoctrineBootstrap
{

    protected static $entityManager;

    public static function getEntityManager()
    {
        if (static::$entityManager) {
            return static::$entityManager;
        }
        $dbParams      = array(
            "driver"=> "pdo_mysql",
            "user"=> "root",
            "password"=> "root",
            "dbname"=> "openinov",
            "host"=> "172.28.128.3"
        );
        $paths         = array(__DIR__."/Entities");
        $isDevMode     = true;
        $config        = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
        static::$entityManager = EntityManager::create($dbParams, $config);
        return static::$entityManager;
    }
}
