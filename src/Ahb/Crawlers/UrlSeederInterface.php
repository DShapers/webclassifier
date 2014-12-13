<?php

namespace Ahb\Crawlers;

interface UrlSeederInterface
{
    public function __construct(array $params);

    public function getUrls();
}
