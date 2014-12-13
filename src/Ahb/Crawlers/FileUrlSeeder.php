<?php

namespace Ahb\Crawlers;

class FileUrlSeeder implements UrlSeederInterface
{
    private $parameters;

    public function __construct(array $params)
    {
        $this->parameters = $params;
    }

    public function getUrls()
    {
        if (!file_exists($this->parameters['file'])) {
            throw new \Exception('FileUrlSeeder cannot open file');
        }
        $jsonUrls = @json_decode(file_get_contents($this->parameters['file']), true);
        return $jsonUrls['urls'];
    }
}
