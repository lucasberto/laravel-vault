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

        $vaultClient = new VaultClient([
            'address' => 'http://localhost:8200',
            'token' => 'test-token'
        ], $mockedClient);

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

        $vaultClient = new VaultClient([
            'address' => 'http://localhost:8200',
            'token' => 'test-token'
        ], $mockedClient);

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

        $vaultClient = new VaultClient([
            'address' => 'http://localhost:8200',
            'token' => 'test-token'
        ], $mockedClient);

        $secret = $vaultClient->getSecret('my/secret', 1);
        
        $this->assertEquals([
            'username' => 'admin',
            'password' => 'secret'
        ], $secret);
    }
}
