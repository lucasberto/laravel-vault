<?php

namespace Lucasberto\LaravelVault\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Lucasberto\LaravelVault\VaultClient;
use Lucasberto\LaravelVault\Tests\TestCase;

class VaultClientTest extends TestCase
{

    protected array $config;
    protected function setUp(): void
    {
        parent::setUp();
        $this->config = [
            'address' => 'http://localhost:8200',
            'token' => 'test-token',
            'version' => 'v1',
            'timeout' => 30
        ];
    }

    private function createMockedClient(array $responses): Client
    {
        $mock = new MockHandler($responses);
        $handlerStack = HandlerStack::create($mock);
        return new Client(['handler' => $handlerStack]);
    }

    public function test_can_get_secret_from_kv2()
    {
        $mockedClient = $this->createMockedClient([
            new Response(200, [], json_encode([
                'data' => [
                    'data' => [
                        'username' => 'admin',
                        'password' => 'secret'
                    ]
                ]
            ]))
        ]);

        $vaultClient = new VaultClient($this->config, $mockedClient);

        $secret = $vaultClient->getSecret('my/secret');
        
        $this->assertEquals([
            'username' => 'admin',
            'password' => 'secret'
        ], $secret);
    }

    public function test_can_store_secret_in_kv2()
    {
        $mockedClient = $this->createMockedClient([
            new Response(200, [], json_encode([
                'data' => [
                    'created_time' => '2023-01-01T00:00:00Z',
                    'version' => 1
                ]
            ]))
        ]);

        $vaultClient = new VaultClient($this->config, $mockedClient);

        $data = [
            'username' => 'admin',
            'password' => 'secret'
        ];

        $response = $vaultClient->putSecret('my/secret', $data);
        
        $this->assertArrayHasKey('data', $response);
        $this->assertArrayHasKey('version', $response['data']);
    }

    public function test_can_get_secret_from_kv1()
    {
        $mockedClient = $this->createMockedClient([
            new Response(200, [], json_encode([
                'data' => [
                    'username' => 'admin',
                    'password' => 'secret'
                ]
            ]))
        ]);

        $vaultClient = new VaultClient($this->config, $mockedClient);

        $secret = $vaultClient->getSecret('my/secret', 1);
        
        $this->assertEquals([
            'username' => 'admin',
            'password' => 'secret'
        ], $secret);
    }

    public function test_can_list_secrets()
{
    $mockedClient = $this->createMockedClient([
        new Response(200, [], json_encode([
            'data' => [
                'keys' => ['secret1', 'secret2']
            ]
        ]))
    ]);

    $vaultClient = new VaultClient($this->config, $mockedClient);
    $result = $vaultClient->listSecrets('my/path');
    
    $this->assertArrayHasKey('data', $result);
    $this->assertCount(2, $result['data']['keys']);
}

public function test_can_delete_secret()
{
    $mockedClient = $this->createMockedClient([
        new Response(204)
    ]);

    $vaultClient = new VaultClient($this->config, $mockedClient);
    $result = $vaultClient->deleteSecret('my/secret');
    
    $this->assertTrue($result);
}

public function test_can_check_seal_status()
{
    $mockedClient = $this->createMockedClient([
        new Response(200, [], json_encode([
            'sealed' => false
        ]))
    ]);

    $vaultClient = new VaultClient($this->config, $mockedClient);
    $result = $vaultClient->isUnsealed();
    
    $this->assertTrue($result);
}

public function test_can_seal_vault()
{
    $mockedClient = $this->createMockedClient([
        new Response(204)
    ]);

    $vaultClient = new VaultClient($this->config, $mockedClient);
    $result = $vaultClient->seal();
    
    $this->assertTrue($result);
}

public function test_can_unseal_vault()
{
    $mockedClient = $this->createMockedClient([
        new Response(204)
    ]);

    $vaultClient = new VaultClient($this->config, $mockedClient);
    $result = $vaultClient->unseal('unseal-key');
    
    $this->assertTrue($result);
}

public function test_can_check_health()
{
    $mockedClient = $this->createMockedClient([
        new Response(200, [], json_encode([
            'initialized' => true,
            'sealed' => false,
            'standby' => false
        ]))
    ]);

    $vaultClient = new VaultClient($this->config, $mockedClient);
    $result = $vaultClient->health();
    
    $this->assertArrayHasKey('initialized', $result);
    $this->assertArrayHasKey('sealed', $result);
    $this->assertArrayHasKey('standby', $result);
}
}
