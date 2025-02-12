<?php

namespace Lucasberto\LaravelVault\Tests;

use Lucasberto\LaravelVault\VaultServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            VaultServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('vault.default', 'main');
        $app['config']->set('vault.servers.main', [
            'address' => 'http://localhost:8200',
            'token' => 'test-token',
            'version' => 'v1',
            'timeout' => 30,
        ]);
    }
}
