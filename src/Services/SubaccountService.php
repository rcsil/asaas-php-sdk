<?php

namespace Asaas\Services;

use Asaas\Support\Config;
use Asaas\Support\HttpClient;

class SubaccountService
{
    protected Config $config;
    protected HttpClient $httpClient;
    private string $basePath;

    public function __construct(HttpClient $http, Config $config)
    {
        $this->httpClient = $http;
        $this->config = $config;
        $this->basePath = sprintf('/%s/accounts', $this->config->get('api_version'));
    }

    /**
     * Create a new subaccount.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function create(array $data): array
    {
        return $this->httpClient->post($this->basePath, $data);
    }

    /**
     * Retrieve a specific subaccount by ID.
     *
     * @param string $id
     * @return array<string, mixed>
     */
    public function get(string $id): array
    {
        return $this->httpClient->get($this->buildPath($id));
    }

    /**
     * List all subaccounts.
     *
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    public function list(array $params = []): array
    {
        return $this->httpClient->get($this->basePath, $params);
    }

    /**
     * Create an API Key for a subaccount.
     *
     * @param string $subaccountId
     * @return array<string, mixed>
     */
    public function createApiKey(string $subaccountId): array
    {
        return $this->httpClient->post($this->buildPath($subaccountId, '/accessTokens'));
    }

    /**
     * List API Keys for a subaccount.
     *
     * @param string $subaccountId
     * @param array<string, mixed> $params
     * @return array<string, mixed>
     */
    public function listApiKeys(string $subaccountId, array $params = []): array
    {
        return $this->httpClient->get($this->buildPath($subaccountId, '/accessTokens'), $params);
    }

    /**
     * Update an API Key for a subaccount.
     *
     * @param string $subaccountId
     * @param string $apiKeyId
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateApiKey(string $subaccountId, string $apiKeyId, array $data): array
    {
        $path = $this->buildPath($subaccountId, sprintf('/accessTokens/%s', $apiKeyId));

        return $this->httpClient->put($path, $data);
    }

    /**
     * Delete an API Key for a subaccount.
     *
     * @param string $subaccountId
     * @param string $apiKeyId
     * @return array<string, mixed>
     */
    public function deleteApiKey(string $subaccountId, string $apiKeyId): array
    {
        $path = $this->buildPath($subaccountId, sprintf('/accessTokens/%s', $apiKeyId));

        return $this->httpClient->delete($path);
    }

    /**
     * Build the URI for a resource.
     *
     * @param string|null $id
     * @param string $suffix
     * @return string
     */
    private function buildPath(?string $id = null, string $suffix = ''): string
    {
        $trimmedId = $id !== null ? trim($id) : null;

        if ($trimmedId !== null && $trimmedId === '') {
            throw new \InvalidArgumentException('Resource ID cannot be empty.');
        }

        $uri = $this->basePath;

        if ($trimmedId !== null) {
            $uri .= '/' . $trimmedId;
        }

        if ($suffix !== '') {
            $uri .= '/' . ltrim($suffix, '/');
        }

        return $uri;
    }

    /**
     * Check pending documents for a subaccount.
     *
     * @param string $apiKey The subaccount's API key.
     * @return array<string, mixed>
     */
    public function checkPendingDocuments(string $apiKey): array
    {
        $path = sprintf('/%s/myAccount/documents', $this->config->get('api_version'));
        return $this->httpClient->get($path, [], ['access_token' => $apiKey]);
    }

    /**
     * Upload a document for a subaccount (resolve a pending document).
     *
     * @param string $apiKey The subaccount's API key.
     * @param string $documentId The document group ID (from checkPendingDocuments).
     * @param string|resource $file The absolute path to the file or a stream resource.
     * @param string|null $type The document type.
     * @param string|null $filename The filename (optional if $file is a path, recommended if $file is a resource).
     * @return array<string, mixed>
     */
    public function uploadDocument(string $apiKey, string $documentId, $file, ?string $type = null, ?string $filename = null): array
    {
        if (is_string($file)) {
            if (!file_exists($file)) {
                throw new \InvalidArgumentException("File not found: {$file}");
            }
            $contents = fopen($file, 'r');
            $filename = $filename ?? basename($file);
        } elseif (is_resource($file)) {
            $contents = $file;
        } else {
            throw new \InvalidArgumentException('File must be a string path or a stream resource.');
        }

        if (!$filename) {
            throw new \InvalidArgumentException('Filename is required when passing a resource.');
        }

        $multipart = [
            [
                'name'     => 'documentFile',
                'contents' => $contents,
                'filename' => $filename,
            ],
        ];

        if ($type) {
            $multipart[] = [
                'name'     => 'type',
                'contents' => $type,
            ];
        }

        $path = sprintf('/%s/myAccount/documents/%s', $this->config->get('api_version'), $documentId);

        return $this->httpClient->postMultipart($path, $multipart, ['access_token' => $apiKey]);
    }

    /**
     * Retrieve a submitted document file details.
     *
     * @param string $apiKey The subaccount's API key.
     * @param string $fileId The uploaded file ID.
     * @return array<string, mixed>
     */
    public function getDocument(string $apiKey, string $fileId): array
    {
        $path = sprintf('/%s/myAccount/documents/files/%s', $this->config->get('api_version'), $fileId);
        return $this->httpClient->get($path, [], ['access_token' => $apiKey]);
    }

    /**
     * Update a submitted document file.
     *
     * @param string $apiKey The subaccount's API key.
     * @param string $documentId The document ID.
     * @param string|resource $file The absolute path to the new file or a stream resource.
     * @param string|null $type The document type.
     * @param string|null $filename The filename (optional if $file is a path, recommended if $file is a resource).
     * @return array<string, mixed>
     */
    public function updateDocument(string $apiKey, string $documentId, $file, ?string $type = null, ?string $filename = null): array
    {
        return $this->uploadDocument($apiKey, $documentId, $file, $type, $filename);
    }

    /**
     * Delete a submitted document file.
     *
     * @param string $apiKey The subaccount's API key.
     * @param string $fileId The uploaded file ID.
     * @return array<string, mixed>
     */
    public function deleteDocument(string $apiKey, string $fileId): array
    {
        $path = sprintf('/%s/myAccount/documents/files/%s', $this->config->get('api_version'), $fileId);
        return $this->httpClient->delete($path, [], null, ['access_token' => $apiKey]);
    }
}
