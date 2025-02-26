# Laravel Vault

A package for simple and easy Laravel and HashiCorp Vault integration.

## Features

- Simple integration with HashiCorp Vault
- Support for multiple Vault servers
- Clean facade-based API

### Supported Secret Engines (will add more in the future):

- KV Secrets Engine v1
- KV Secrets Engine v2

## Requirements

- PHP 8.1 or higher
- Laravel 9, 10, or 11
- HashiCorp Vault 1.12 or higher

## Installation

You can install the package via composer:

```bash
composer require lucasberto/laravel-vault
```

## Configuration

After installing the package, you need to publish the configuration file:

```bash
php artisan vendor:publish --tag=vault-config
```

This will create a `config/vault.php` file in your project.

Also, you need to add the following lines to your `.env` file:

```bash
# Required configs
VAULT_ADDR=http://127.0.0.1:8200
VAULT_TOKEN=your-token-here

# Optional configs
VAULT_TIMEOUT=10   # Default: 30
VAULT_KV_ROOT=kv-v2 # Default: secret
```

If you want to use multiple Vault servers, you can add more addresses and tokens to the `.env` file:

```bash
VAULT_SECONDARY_ADDR=http://vault2.example.com:8200
VAULT_SECONDARY_TOKEN=another-token

# Optional configs
VAULT_SECONDARY_TIMEOUT=20   # Default: 30
VAULT_SECONDARY_KV_ROOT=kv-v2 # Default: secret
```

And update the `config/vault.php` file accordingly.

```php
'servers' => [
        'main' => [
            'address' => env('VAULT_ADDR', 'http://127.0.0.1:8200'),
            'token' => env('VAULT_TOKEN'),
            'timeout' => env('VAULT_TIMEOUT', 30),
            'kv_root' => env('VAULT_KV_ROOT', 'secret'),
        ],
        'secondary' => [
            'address' => env('VAULT_SECONDARY_ADDR'),
            'token' => env('VAULT_SECONDARY_TOKEN'),
            'timeout' => env('VAULT_SECONDARY_TIMEOUT', 30),
            'kv_root' => env('VAULT_SECONDARY_KV_ROOT', 'secret'),
        ],
    ],
```

## Usage

### Basic Usage

```php
use Lucasberto\LaravelVault\Facades\Vault;


// List secrets (KV v2)
$secrets = Vault::listSecrets('path/to/secrets');
// List secrets (KV v1)
$secrets = Vault::listSecrets('path/to/secrets', 1);


// Get a secret (KV v2)
$secret = Vault::getSecret('path/to/secret');
// Using KV v1
$secret = Vault::getSecret('path/to/secret', 1);


// Store a secret (KV v2)
Vault::putSecret('path/to/secret', [
    'username' => 'admin',
    'password' => 'secret'
]);
// Store a secret (KV v1)
Vault::putSecret('path/to/secret', [
    'username' => 'admin',
    'password' => 'secret'
], 1);


// Delete a secret (KV v2)
Vault::deleteSecret('path/to/secret');
// Delete a secret (KV v1)
Vault::deleteSecret('path/to/secret', 1);

// Check if vault is unsealed
$isUnsealed = Vault::isUnsealed();

// Seal vault
$sealed = Vault::seal();

// Unseal vault (one call per key, after n calls, vault is unsealed)
Vault::unseal('key');

// Get vault health
$health = Vault::health();

// It is also possible to use a custom client (of type GuzzleHttp\Client)
$config = app()->config['vault']['servers']['main'];
$httpClient = new \GuzzleHttp\Client([
    'base_uri' => $config['address'],
    'headers' => [
        'X-Vault-Token' => $config['token'],
    ],
    'timeout' => $config['timeout'],
]);
$vaultClient = new Lucasberto\LaravelVault\VaultClient($config, $httpClient);
$vaultClient->getSecret('path/to/secret');
```

### Multiple servers

```php
// Use default connection
$secret = Vault::getSecret('path/to/secret');

// Use specific connection
$secret = Vault::connection('secondary')->getSecret('path/to/secret');
```
