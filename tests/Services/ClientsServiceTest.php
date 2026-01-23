<?php

namespace Asaas\Tests\Services;

use Asaas\Services\ClientsService;
use Asaas\Support\Config;
use Asaas\Support\HttpClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class ClientsServiceTest extends TestCase
{
    private function createHttpClient(ClientInterface $guzzleClient): HttpClient
    {
        $config = $this->createMock(Config::class);
        $config->method('getBaseUrl')->willReturn('https://api.asaas.com');
        return new HttpClient('api_key', $config, $guzzleClient);
    }

    private function createConfigMock(): Config
    {
        $config = $this->createMock(Config::class);
        $config->method('get')->willReturnCallback(function ($key, $default = null) {
            if ($key === 'api_version') {
                return 'v3';
            }
            return $default;
        });
        return $config;
    }

    public function testList()
    {
        $guzzleClient = $this->createMock(ClientInterface::class);
        $guzzleClient->expects($this->once())
             ->method('request')
             ->with(
                 'GET',
                 '/v3/customers',
                 $this->callback(function ($options) {
                     return isset($options['query']) && $options['query'] === ['limit' => 10];
                 })
             )
             ->willReturn(new Response(200, [], json_encode(['data' => []])));

        $http = $this->createHttpClient($guzzleClient);
        $config = $this->createConfigMock();

        $service = new ClientsService($http, $config);
        $result = $service->list(['limit' => 10]);

        $this->assertEquals(['data' => []], $result);
    }

    public function testGet()
    {
        $guzzleClient = $this->createMock(ClientInterface::class);
        $guzzleClient->expects($this->once())
             ->method('request')
             ->with('GET', '/v3/customers/cus_123')
             ->willReturn(new Response(200, [], json_encode(['id' => 'cus_123'])));

        $http = $this->createHttpClient($guzzleClient);
        $config = $this->createConfigMock();

        $service = new ClientsService($http, $config);
        $result = $service->get('cus_123');

        $this->assertEquals(['id' => 'cus_123'], $result);
    }

    public function testCreate()
    {
        $guzzleClient = $this->createMock(ClientInterface::class);
        $guzzleClient->expects($this->once())
             ->method('request')
             ->with(
                 'POST',
                 '/v3/customers',
                 $this->callback(function ($options) {
                     return isset($options['json']) && $options['json'] === ['name' => 'John Doe'];
                 })
             )
             ->willReturn(new Response(200, [], json_encode(['id' => 'cus_123'])));

        $http = $this->createHttpClient($guzzleClient);
        $config = $this->createConfigMock();

        $service = new ClientsService($http, $config);
        $result = $service->create(['name' => 'John Doe']);

        $this->assertEquals(['id' => 'cus_123'], $result);
    }

    public function testUpdate()
    {
        $guzzleClient = $this->createMock(ClientInterface::class);
        $guzzleClient->expects($this->once())
             ->method('request')
             ->with(
                 'PUT',
                 '/v3/customers/cus_123',
                 $this->callback(function ($options) {
                     return isset($options['json']) && $options['json'] === ['name' => 'Jane Doe'];
                 })
             )
             ->willReturn(new Response(200, [], json_encode(['id' => 'cus_123'])));

        $http = $this->createHttpClient($guzzleClient);
        $config = $this->createConfigMock();

        $service = new ClientsService($http, $config);
        $result = $service->update('cus_123', ['name' => 'Jane Doe']);

        $this->assertEquals(['id' => 'cus_123'], $result);
    }

    public function testDelete()
    {
        $guzzleClient = $this->createMock(ClientInterface::class);
        $guzzleClient->expects($this->once())
             ->method('request')
             ->with('DELETE', '/v3/customers/cus_123')
             ->willReturn(new Response(200, [], json_encode(['deleted' => true])));

        $http = $this->createHttpClient($guzzleClient);
        $config = $this->createConfigMock();

        $service = new ClientsService($http, $config);
        $result = $service->delete('cus_123');

        $this->assertEquals(['deleted' => true], $result);
    }

    public function testNotifications()
    {
        $guzzleClient = $this->createMock(ClientInterface::class);
        $guzzleClient->expects($this->once())
             ->method('request')
             ->with('GET', '/v3/customers/cus_123/notifications')
             ->willReturn(new Response(200, [], json_encode(['data' => []])));

        $http = $this->createHttpClient($guzzleClient);
        $config = $this->createConfigMock();

        $service = new ClientsService($http, $config);
        $result = $service->notifications('cus_123');

        $this->assertEquals(['data' => []], $result);
    }
}
