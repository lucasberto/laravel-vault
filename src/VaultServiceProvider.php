<?php

namespace Lucasberto\LaravelVault;

use Illuminate\Support\ServiceProvider;
use function config_path;


class VaultServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/vault.php', 'vault');

        $this->app->singleton('vault', function ($app) {
            return new VaultManager($app['config']['vault']);
        });
    }

    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/vault.php' => config_path('vault.php'),
        ], 'vault-config');
    }
}

