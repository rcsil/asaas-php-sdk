<?php

namespace Asaas\Services;

use Asaas\Support\Config;
use Asaas\Support\HttpClient;

class ClientsService
{
    protected Config $config;
    protected HttpClient $http;
    private string $customerBasePath;

    public function __construct(HttpClient $http, Config $config)
    {
        $this->http   = $http;
        $this->config = $config;
        $this->customerBasePath = sprintf('/%s/customers', $this->config->get('api_version'));
    }

    /**
     * Retrieve paginated customers list.
     *
     * @param array<string, mixed> $params
     */
    public function list(array $params = []): array
    {
        return $this->http->get($this->customerBasePath, $params);
    }

    /**
     * Retrieve a single customer by id.
     */
    public function get(string $id): array
    {
        return $this->http->get($this->buildCustomerUri($id));
    }

    /**
     * Create a new customer.
     *
     * @param array<string, mixed> $data
     */
    public function create(array $data): array
    {
        return $this->http->post($this->customerBasePath, $data);
    }

    /**
     * Update an existing customer.
     *
     * @param array<string, mixed> $data
     */
    public function update(string $id, array $data): array
    {
        return $this->http->put($this->buildCustomerUri($id), $data);
    }

    /**
     * Remove a customer.
     */
    public function delete(string $id): array
    {
        return $this->http->delete($this->buildCustomerUri($id));
    }

    /**
     * List notification preferences for a customer.
     */
    public function notifications(string $id): array
    {
        return $this->http->get($this->buildCustomerUri($id, 'notifications'));
    }

    /**
     * Build customer-related endpoint paths while validating the identifier.
     */
    private function buildCustomerUri(?string $id = null, string $suffix = ''): string
    {
        $trimmedId = $id !== null ? trim($id) : null;

        if ($trimmedId !== null && $trimmedId === '') {
            throw new \InvalidArgumentException('Customer id cannot be empty.');
        }

        $uri = $this->customerBasePath;

        if ($trimmedId !== null) {
            $uri .= '/' . $trimmedId;
        }

        if ($suffix !== '') {
            $uri .= '/' . ltrim($suffix, '/');
        }

        return $uri;
    }
}
