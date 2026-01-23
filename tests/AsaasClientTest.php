<?php

namespace Asaas\Tests;

use Asaas\AsaasClient;
use Asaas\Services\ClientsService;
use Asaas\Services\PaymentsService;
use Asaas\Services\SubaccountService;
use PHPUnit\Framework\TestCase;

class AsaasClientTest extends TestCase
{
    public function testConstructWithValidApiKey()
    {
        $client = new AsaasClient('valid_api_key');
        $this->assertInstanceOf(AsaasClient::class, $client);
    }

    public function testConstructWithInvalidApiKey()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('API key cannot be empty.');
        new AsaasClient('   ');
    }

    public function testServicesAreInstantiatedLazilyAndCached()
    {
        $client = new AsaasClient('valid_api_key');

        $payments1 = $client->payments();
        $this->assertInstanceOf(PaymentsService::class, $payments1);
        $payments2 = $client->payments();
        $this->assertSame($payments1, $payments2);

        $clients1 = $client->clients();
        $this->assertInstanceOf(ClientsService::class, $clients1);
        $clients2 = $client->clients();
        $this->assertSame($clients1, $clients2);

        $subaccount1 = $client->subaccountService();
        $this->assertInstanceOf(SubaccountService::class, $subaccount1);
        $subaccount2 = $client->subaccountService();
        $this->assertSame($subaccount1, $subaccount2);
    }
}
