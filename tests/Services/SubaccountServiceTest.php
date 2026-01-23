<?php

namespace Asaas\Tests\Services;

use Asaas\Services\SubaccountService;
use Asaas\Support\Config;
use Asaas\Support\HttpClient;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class SubaccountServiceTest extends TestCase
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

    public function testCreate()
    {
        $guzzleClient = $this->createMock(ClientInterface::class);
        $guzzleClient->expects($this->once())
             ->method('request')
             ->with(
                 'POST',
                 '/v3/accounts',
                 $this->callback(function ($options) {
                     return isset($options['json']) && $options['json'] === ['name' => 'Test'];
                 })
             )
             ->willReturn(new Response(200, [], json_encode(['id' => '123'])));

        $http = $this->createHttpClient($guzzleClient);
        $config = $this->createConfigMock();

        $service = new SubaccountService($http, $config);
        $result = $service->create(['name' => 'Test']);

        $this->assertEquals(['id' => '123'], $result);
    }

    public function testGet()
    {
        $guzzleClient = $this->createMock(ClientInterface::class);
        $guzzleClient->expects($this->once())
             ->method('request')
             ->with('GET', '/v3/accounts/123')
             ->willReturn(new Response(200, [], json_encode(['id' => '123'])));

        $http = $this->createHttpClient($guzzleClient);
        $config = $this->createConfigMock();

        $service = new SubaccountService($http, $config);
        $result = $service->get('123');

        $this->assertEquals(['id' => '123'], $result);
    }

    public function testList()
    {
        $guzzleClient = $this->createMock(ClientInterface::class);
        $guzzleClient->expects($this->once())
             ->method('request')
             ->with(
                 'GET',
                 '/v3/accounts',
                 $this->callback(function ($options) {
                     return isset($options['query']) && $options['query'] === ['limit' => 10];
                 })
             )
             ->willReturn(new Response(200, [], json_encode(['data' => []])));

        $http = $this->createHttpClient($guzzleClient);
        $config = $this->createConfigMock();

        $service = new SubaccountService($http, $config);
        $result = $service->list(['limit' => 10]);

        $this->assertEquals(['data' => []], $result);
    }

    public function testCreateApiKey()
    {
        $guzzleClient = $this->createMock(ClientInterface::class);
        $guzzleClient->expects($this->once())
             ->method('request')
             ->with('POST', '/v3/accounts/123/accessTokens')
             ->willReturn(new Response(200, [], json_encode(['apiKey' => 'key'])));

        $http = $this->createHttpClient($guzzleClient);
        $config = $this->createConfigMock();

        $service = new SubaccountService($http, $config);
        $result = $service->createApiKey('123');

        $this->assertEquals(['apiKey' => 'key'], $result);
    }

    public function testListApiKeys()
    {
        $guzzleClient = $this->createMock(ClientInterface::class);
        $guzzleClient->expects($this->once())
             ->method('request')
             ->with(
                 'GET', 
                 '/v3/accounts/123/accessTokens',
                 $this->callback(function ($options) {
                     return isset($options['query']) && $options['query'] === ['active' => true];
                 })
             )
             ->willReturn(new Response(200, [], json_encode(['data' => []])));

        $http = $this->createHttpClient($guzzleClient);
        $config = $this->createConfigMock();

        $service = new SubaccountService($http, $config);
        $result = $service->listApiKeys('123', ['active' => true]);

        $this->assertEquals(['data' => []], $result);
    }

    public function testUpdateApiKey()
    {
        $guzzleClient = $this->createMock(ClientInterface::class);
        $guzzleClient->expects($this->once())
             ->method('request')
             ->with(
                 'PUT',
                 '/v3/accounts/123/accessTokens/key_id',
                 $this->callback(function ($options) {
                     return isset($options['json']) && $options['json'] === ['active' => false];
                 })
             )
             ->willReturn(new Response(200, [], json_encode(['success' => true])));

        $http = $this->createHttpClient($guzzleClient);
        $config = $this->createConfigMock();

        $service = new SubaccountService($http, $config);
        $result = $service->updateApiKey('123', 'key_id', ['active' => false]);

        $this->assertEquals(['success' => true], $result);
    }

    public function testDeleteApiKey()
    {
        $guzzleClient = $this->createMock(ClientInterface::class);
        $guzzleClient->expects($this->once())
             ->method('request')
             ->with('DELETE', '/v3/accounts/123/accessTokens/key_id')
             ->willReturn(new Response(200, [], json_encode(['success' => true])));

        $http = $this->createHttpClient($guzzleClient);
        $config = $this->createConfigMock();

        $service = new SubaccountService($http, $config);
        $result = $service->deleteApiKey('123', 'key_id');

        $this->assertEquals(['success' => true], $result);
    }

    public function testCheckPendingDocuments()
    {
        $guzzleClient = $this->createMock(ClientInterface::class);
        $guzzleClient->expects($this->once())
             ->method('request')
             ->with(
                 'GET', 
                 '/v3/myAccount/documents',
                 $this->callback(function ($options) {
                     return isset($options['headers']['access_token']) 
                         && $options['headers']['access_token'] === 'sub_token';
                 })
             )
             ->willReturn(new Response(200, [], json_encode(['data' => []])));

        $http = $this->createHttpClient($guzzleClient);
        $config = $this->createConfigMock();

        $service = new SubaccountService($http, $config);
        $result = $service->checkPendingDocuments('sub_token');

        $this->assertEquals(['data' => []], $result);
    }

    public function testUploadDocument()
    {
        $guzzleClient = $this->createMock(ClientInterface::class);
        $guzzleClient->expects($this->once())
             ->method('request')
             ->with(
                 'POST',
                 '/v3/myAccount/documents/doc_id',
                 $this->callback(function ($options) {
                     return isset($options['multipart']) 
                        && $options['multipart'][0]['name'] === 'file'
                        && isset($options['headers']['access_token'])
                        && $options['headers']['access_token'] === 'sub_token';
                 })
             )
             ->willReturn(new Response(200, [], json_encode(['id' => 'doc_id', 'status' => 'SENT'])));

        $http = $this->createHttpClient($guzzleClient);
        $config = $this->createConfigMock();
        
        $tempFile = tempnam(sys_get_temp_dir(), 'test_doc');
        file_put_contents($tempFile, 'test content');

        $service = new SubaccountService($http, $config);
        $result = $service->uploadDocument('sub_token', 'doc_id', $tempFile);

        unlink($tempFile);

        $this->assertEquals(['id' => 'doc_id', 'status' => 'SENT'], $result);
    }

    public function testGetDocument()
    {
        $guzzleClient = $this->createMock(ClientInterface::class);
        $guzzleClient->expects($this->once())
             ->method('request')
             ->with(
                 'GET', 
                 '/v3/myAccount/documents/files/file_id',
                 $this->callback(function ($options) {
                     return isset($options['headers']['access_token']) 
                         && $options['headers']['access_token'] === 'sub_token';
                 })
             )
             ->willReturn(new Response(200, [], json_encode(['id' => 'file_id'])));

        $http = $this->createHttpClient($guzzleClient);
        $config = $this->createConfigMock();

        $service = new SubaccountService($http, $config);
        $result = $service->getDocument('sub_token', 'file_id');

        $this->assertEquals(['id' => 'file_id'], $result);
    }

    public function testUpdateDocument()
    {
        $guzzleClient = $this->createMock(ClientInterface::class);
        $guzzleClient->expects($this->once())
             ->method('request')
             ->with(
                 'POST',
                 '/v3/myAccount/documents/files/file_id',
                 $this->callback(function ($options) {
                     return isset($options['multipart'])
                        && isset($options['headers']['access_token'])
                        && $options['headers']['access_token'] === 'sub_token';
                 })
             )
             ->willReturn(new Response(200, [], json_encode(['success' => true])));

        $http = $this->createHttpClient($guzzleClient);
        $config = $this->createConfigMock();

        $tempFile = tempnam(sys_get_temp_dir(), 'test_doc_upd');
        file_put_contents($tempFile, 'updated content');

        $service = new SubaccountService($http, $config);
        $result = $service->updateDocument('sub_token', 'file_id', $tempFile);
        
        unlink($tempFile);

        $this->assertEquals(['success' => true], $result);
    }

    public function testDeleteDocument()
    {
        $guzzleClient = $this->createMock(ClientInterface::class);
        $guzzleClient->expects($this->once())
             ->method('request')
             ->with(
                 'DELETE', 
                 '/v3/myAccount/documents/files/file_id',
                 $this->callback(function ($options) {
                     return isset($options['headers']['access_token']) 
                         && $options['headers']['access_token'] === 'sub_token';
                 })
             )
             ->willReturn(new Response(200, [], json_encode(['success' => true])));

        $http = $this->createHttpClient($guzzleClient);
        $config = $this->createConfigMock();

        $service = new SubaccountService($http, $config);
        $result = $service->deleteDocument('sub_token', 'file_id');

        $this->assertEquals(['success' => true], $result);
    }
}
