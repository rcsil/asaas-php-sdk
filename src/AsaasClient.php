<?php

namespace Asaas;

use Asaas\Services\ClientsService;
use Asaas\Services\PaymentsService;
use Asaas\Support\Config;
use Asaas\Support\HttpClient;

class AsaasClient
{
    private string $apiKey;
    private Config $config;
    private HttpClient $http;
    private ?PaymentsService $paymentsService = null;
    private ?ClientsService $clientsService   = null;

    /**
     * @param array<string, mixed> $config
     */
    public function __construct(string $apiKey, array $config = [])
    {
        $this->apiKey = $this->sanitizeApiKey($apiKey);
        $this->config = new Config($config);
        $this->http   = new HttpClient($this->apiKey, $this->config);
    }

    /**
     * Lazily return a payments service instance sharing the same HTTP client.
     */
    public function payments(): PaymentsService
    {
        if ($this->paymentsService === null) {
            $this->paymentsService = new PaymentsService($this->http, $this->config);
        }

        return $this->paymentsService;
    }

    /**
     * Lazily return a clients service instance sharing the same HTTP client.
     */
    public function clients(): ClientsService
    {
        if ($this->clientsService === null) {
            $this->clientsService = new ClientsService($this->http, $this->config);
        }

        return $this->clientsService;
    }

    /**
     * Validate and normalize the provided API key.
     */
    private function sanitizeApiKey(string $apiKey): string
    {
        $trimmed = trim($apiKey);

        if ($trimmed === '') {
            throw new \InvalidArgumentException('API key cannot be empty.');
        }

        return $trimmed;
    }
}
