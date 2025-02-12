<?php

namespace Lucasberto\LaravelVault;

use GuzzleHttp\Client;

class VaultClient {
    protected Client $client;
    protected array $config;

    public function __construct(array $config, ?Client $client = null)
    {
        $this->config = $config;
        $this->client = $client ?? new Client([
            'base_uri' => $config['address'],
            'timeout' => $config['timeout'],
            'headers' => [
                'X-Vault-Token' => $config['token'],
            ]
        ]);
    }

    public function getSecret(string $path, int $kvVersion = 2): array {
        $fullPath = $kvVersion == 2 ? "secret/data/{$path}" : "secret/{$path}";
        $response = $this->client->get("/v1/{$fullPath}");

        $data = json_decode($response->getBody(), true);

        return $kvVersion === 2 ? $data['data']['data'] : $data['data'];
    }

    public function putSecret(string $path, array $data, int $kvVersion = 2): array {
        $fullPath = $kvVersion === 2 ? "secret/data/{$path}" : "secret/{$path}";
        $payload = $kvVersion === 2 ? ['data' => $data] : $data;

        $response = $this->client->put("/v1/{$fullPath}", ['json' => $payload]);

        return json_decode($response->getBody(), true);
    }

    public function listSecrets(string $path, int $kvVersion = 2): array {
        $fullPath = $kvVersion === 2 ? "secret/metadata/{$path}" : "secret/{$path}";
        $response = $this->client->get("/v1/{$fullPath}");
        return json_decode($response->getBody(), true);
    }

    public function deleteSecret(string $path, int $kvVersion = 2): bool {
        $fullPath = $kvVersion === 2 ? "secret/data/{$path}" : "secret/{$path}";
        $response = $this->client->delete("/v1/{$fullPath}");
        return $response->getStatusCode() === 204;
    }

    public function isUnsealed(): bool {
        $response = $this->client->get('/v1/sys/seal-status');
        $data = json_decode($response->getBody(), true);
        return !$data['sealed'] ?? false;
    }

    public function seal(): bool {
        $response = $this->client->put('/v1/sys/seal');
        return $response->getStatusCode() === 204;
    }

    public function unseal(string $key): array {
        $response = $this->client->put('/v1/sys/unseal', ['json' => ['key' => $key]]);
        return json_decode($response->getBody(), true);
    }

    public function health(): array {
        $response = $this->client->get('/v1/sys/health');
        return json_decode($response->getBody(), true);
    }
    
}