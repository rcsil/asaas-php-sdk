<?php

namespace Asaas\Services;

use Asaas\Support\Config;
use Asaas\Support\HttpClient;

class PaymentsService
{
    protected Config $config;
    protected HttpClient $http;

    public function __construct(HttpClient $http, Config $config)
    {
        $this->config = $config;
        $this->http   = $http;
    }

    /**
     * Create new Asaas charge
     * 
     * @param array $data
     * @return array
     */
    public function create(array $data): array
    {
        $apiVersion = $this->config->get('api_version');
        $uri        = "/${apiVersion}/payments";

        return $this->http->post($uri, $data);
    }
}