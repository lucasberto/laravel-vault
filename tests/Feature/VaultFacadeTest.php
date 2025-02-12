<?php

namespace Lucasberto\LaravelVault\Tests\Feature;

use Lucasberto\LaravelVault\Facades\Vault;
use Lucasberto\LaravelVault\Tests\TestCase;

class VaultFacadeTest extends TestCase
{
    public function test_facade_is_accessible()
    {
        $this->assertInstanceOf(\Lucasberto\LaravelVault\VaultManager::class, Vault::getFacadeRoot());
    }

    public function test_can_switch_connections()
    {
        $this->assertNotNull(Vault::connection('main'));
    }
}
