# Laravel Vault

A robust Laravel integration for HashiCorp Vault, providing secure secret management with an elegant Laravel-friendly API.

## Features

- Simple integration with HashiCorp Vault
- Support for multiple Vault servers
- Compatible with KV Secrets Engine v1 and v2
- Laravel 9, 10, and 11 support
- Clean facade-based API

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
VAULT_ADDR=http://127.0.0.1:8200
VAULT_TOKEN=your-token-here
```

If you want to use multiple Vault servers, you can add more addresses and tokens to the `.env` file:

```bash
VAULT_SECONDARY_ADDR=http://vault2.example.com:8200
VAULT_SECONDARY_TOKEN=another-token
```

And update the `config/vault.php` file accordingly.

```php
'servers' => [
        'main' => [
            'address' => env('VAULT_ADDR', 'http://127.0.0.1:8200'),
            'token' => env('VAULT_TOKEN'),
            'version' => env('VAULT_VERSION', 'v1'),
            'timeout' => env('VAULT_TIMEOUT', 30),
        ],
        'secondary' => [
            'address' => env('VAULT_SECONDARY_ADDR'),
            'token' => env('VAULT_SECONDARY_TOKEN'),
            'version' => env('VAULT_SECONDARY_VERSION', 'v1'),
            'timeout' => env('VAULT_SECONDARY_TIMEOUT', 30),
        ],
    ],
```

## Usage

### Basic Usage

```php
use Lucasberto\LaravelVault\Facades\Vault;

// Get a secret (KV v2)
$secret = Vault::getSecret('path/to/secret');

// Store a secret (KV v2)
Vault::putSecret('path/to/secret', [
    'username' => 'admin',
    'password' => 'secret'
]);

// Using KV v1
$secret = Vault::getSecret('path/to/secret', 1);
```

### Multiple servers

```php
// Use default connection
$secret = Vault::getSecret('path/to/secret');

// Use specific connection
$secret = Vault::connection('secondary')->getSecret('path/to/secret');
```
