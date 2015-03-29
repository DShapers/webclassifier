<?php

require_once __DIR__.'/../lib/autoload.php';
header("Access-Control-Allow-Origin: *");
$app = new \Ahb\BaseApplication();
$app['debug'] = true;
$app->init();
$app->run();
