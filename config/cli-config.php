<?php
require "lib/autoload.php";

use Ahb\DoctrineBootstrap;
use Doctrine\ORM\Tools\Console\ConsoleRunner;

return ConsoleRunner::createHelperSet(DoctrineBootstrap::getEntityManager());
