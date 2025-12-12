<?php

namespace Asaas\Support;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use JsonException;

final class HttpClient
{
    private ClientInterface $client;
    private string $apiKey;
    private Config $config;
    private array $defaultHeaders;

    public function __construct(
        string $apiKey,
        Config $config,
        ?ClientInterface $client = null
    )
    {
        $this->apiKey         = $apiKey;
        $this->config         = $config;
        $this->defaultHeaders = [
            'Accept'       => 'application/json',
            'Content-Type' => 'application/json',
            'access_token' => $apiKey,
        ];

        $this->client = $client ?? new Client([
            'base_uri' => $config->getBaseUrl(),
            'headers'  => $this->defaultHeaders,
        ]);
    }

    /**
     * Execute a GET request with optional query params and headers.
     *
     * @param array<string, mixed> $query
     * @param array<string, string> $headers
     */
    public function get(
        string $uri,
        array $query   = [],
        array $headers = []
    ): array
    {
        return $this->request('GET', $uri, $query, null, $headers);
    }

    /**
     * Execute a POST request with JSON payload and optional headers.
     *
     * @param array<string, mixed> $payload
     * @param array<string, string> $headers
     */
    public function post(
        string $uri,
        array $payload = [],
        array $headers = []
    ): array
    {
        return $this->request('POST', $uri, [], $payload, $headers);
    }

    /**
     * Execute a PUT request with JSON payload and optional headers.
     *
     * @param array<string, mixed> $payload
     * @param array<string, string> $headers
     */
    public function put(
        string $uri, 
        array $payload = [],
        array $headers = []
    ): array
    {
        return $this->request('PUT', $uri, [], $payload, $headers);
    }

    /**
     * Execute a DELETE request with optional query, payload and headers.
     *
     * @param array<string, mixed> $query
     * @param array<string, mixed>|null $payload
     * @param array<string, string> $headers
     */
    public function delete(
        string $uri,
        array $query    = [],
        ?array $payload = null,
        array $headers  = []
    ): array
    {
        return $this->request('DELETE', $uri, $query, $payload, $headers);
    }

    /**
     * Prepare and execute the HTTP request.
     *
     * @param array<string, mixed> $query
     * @param array<string, mixed>|null $payload
     * @param array<string, string> $headers
     */
    private function request(
        string $method, 
        string $uri, 
        array $query    = [], 
        ?array $payload = null, 
        array $headers  = []
    ): array
    {
        $uri = trim($uri);

        if ($uri === '') {
            throw new \InvalidArgumentException('Request URI cannot be empty.');
        }

        $options = $this->buildOptions($query, $payload, $headers);

        try {
            $response = $this->client->request($method, $uri, $options);

            return $this->decodeJson((string) $response->getBody());
        } catch (RequestException $exception) {
            return $this->formatException($exception);
        } catch (JsonException $exception) {
            return [
                'success' => false,
                'error'   => [
                    'message' => 'Resposta inválida (JSON malformado).',
                    'detail'  => $exception->getMessage(),
                ],
            ];
        } catch (\Throwable $exception) {
            return [
                'success' => false,
                'error'   => [
                    'message' => $exception->getMessage(),
                ],
            ];
        }
    }

    /**
     * Decode JSON response to associative array.
     *
     * @return array<string, mixed>
     */
    private function decodeJson(string $json): array
    {
        if ($json === '') {
            return [];
        }

        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return $decoded;
    }

    /**
     * Standardize HTTP error responses.
     *
     * @return array<string, mixed>
     */
    private function formatException(RequestException $exception): array
    {
        $response   = $exception->getResponse();
        $statusCode = $response?->getStatusCode();
        $errorBody  = null;

        if ($response !== null) {
            $raw = (string) $response->getBody();

            try {
                $errorBody = $raw === '' ? [] : $this->decodeJson($raw);
            } catch (JsonException) {
                $errorBody = ['raw' => $raw];
            }
        }

        return [
            'success' => false,
            'error'   => [
                'message'     => $exception->getMessage(),
                'status_code' => $statusCode,
                'response'    => $errorBody ?? ['error' => $exception->getMessage()],
            ],
        ];
    }

    /**
     * Build request options merging defaults with user-provided headers/query/payload.
     *
     * @param array<string, mixed> $query
     * @param array<string, mixed>|null $payload
     * @param array<string, string> $headers
     * @return array<string, mixed>
     */
    private function buildOptions(array $query, ?array $payload, array $headers): array
    {
        $options = [
            RequestOptions::HEADERS => array_merge($this->defaultHeaders, $headers),
        ];

        if ($query !== []) {
            $options[RequestOptions::QUERY] = $query;
        }

        if ($payload !== null) {
            $options[RequestOptions::JSON] = $payload;
        }

        return $options;
    }
}
