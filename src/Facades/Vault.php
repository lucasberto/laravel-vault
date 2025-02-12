<?php

namespace Lucasberto\LaravelVault\Facades;

use Illuminate\Support\Facades\Facade;

class Vault extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'vault';
    }
}
