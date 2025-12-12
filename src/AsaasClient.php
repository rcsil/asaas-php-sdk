<?php

namespace Asaas;

use Asaas\Services\PaymentsService;
use Asaas\Support\Config;
use Asaas\Support\HttpClient;

class AsaasClient
{
    protected string $apiKey;
    protected Config $config;
    protected HttpClient $http;

    public function __construct(
        string $apiKey,
        array $config = [],
        HttpClient $http
    )
    {
        $this->apiKey = $apiKey;
        $this->config = new Config($config);
        $this->http   = $http;
    }

    public function payments(): PaymentsService
    {
        return new PaymentsService($this->http, $this->config);
    }
}