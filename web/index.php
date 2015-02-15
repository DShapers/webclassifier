<?php

require_once __DIR__.'/../lib/autoload.php';

$app = new \Ahb\BaseApplication();
$app['debug'] = true;
$app->init();
$app->run();
