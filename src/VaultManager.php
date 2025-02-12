<?php

namespace Lucasberto\LaravelVault;

class VaultManager
{
    protected array $config;
    protected array $clients = [];

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function connection(?string $name = null)
    {
        $name = $name ?: $this->config['default'];

        if (!isset($this->clients[$name])) {
            $this->clients[$name] = $this->makeClient($name);
        }

        return $this->clients[$name];
    }

    protected function makeClient(string $name)
    {
        $config = $this->config['servers'][$name];
        return new VaultClient($config);
    }

    public function __call($method, $parameters)
    {
        return $this->connection()->$method(...$parameters);
    }
}
