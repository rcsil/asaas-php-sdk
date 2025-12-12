<?php

namespace Asaas\Services;

use Asaas\Support\Config;
use Asaas\Support\HttpClient;

class PaymentsService
{
    private Config $config;
    private HttpClient $http;
    private string $paymentsBasePath;

    public function __construct(HttpClient $http, Config $config)
    {
        $this->config = $config;
        $this->http   = $http;
        $this->paymentsBasePath = sprintf('/%s/payments', $this->config->get('api_version'));
    }

    /**
     * Create new Asaas charge.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): array
    {
        return $this->http->post($this->buildPaymentUri(), $data);
    }

    /**
     * Build payment-related endpoint paths while validating the identifier.
     */
    private function buildPaymentUri(?string $id = null, string $suffix = ''): string
    {
        $trimmedId = $id !== null ? trim($id) : null;

        if ($trimmedId !== null && $trimmedId === '') {
            throw new \InvalidArgumentException('Payment id cannot be empty.');
        }

        $uri = $this->paymentsBasePath;

        if ($trimmedId !== null) {
            $uri .= '/' . $trimmedId;
        }

        if ($suffix !== '') {
            $uri .= '/' . ltrim($suffix, '/');
        }

        return $uri;
    }
}
