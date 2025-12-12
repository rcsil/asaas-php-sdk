<?php

namespace Asaas\Support;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class HttpClient
{
    protected Client $client;
    protected string $apiKey;
    protected Config $config;

    public function __construct(string $apiKey, Config $config)
    {
        $this->apiKey = $apiKey;
        $this->config = $config;

        $this->client = new Client([
            'base_uri' => $config->getBaseUrl(),
            'headers' => [
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
                'access_token' => $apiKey,
            ]
        ]);
    }

    public function post(string $uri, array $data): array
    {
        try {
            $response = $this->client->post($uri, [
                'json' => $data
            ]);

            return json_decode($response->getBody()->getContents(), true);
        } catch (RequestException $exception) {
            $errorResponse = $exception->getResponse()
                ? json_decode($exception->getResponse()->getBody()->getContents(), true)
                : ['error' => $exception->getMessage()];

            return [
                'success' => false,
                'error'   => $errorResponse
            ];
        }
    }
}