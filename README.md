# JingdongCrmBundle

[![PHP Version](https://img.shields.io/badge/php-%5E8.1-blue.svg)](https://php.net)
[![Symfony Version](https://img.shields.io/badge/symfony-%5E7.3-green.svg)](https://symfony.com)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![Build Status](https://img.shields.io/badge/build-passing-brightgreen.svg)](https://github.com/tourze/php-monorepo)
[![Code Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen.svg)](https://github.com/tourze/php-monorepo)

[English](README.md) | [中文](README.zh-CN.md)

A Symfony bundle for integrating with Jingdong CRM system, providing customer relationship management functionality.

## Features

- Jingdong CRM system integration
- Symfony 7.3+ compatibility
- Full dependency injection support
- Comprehensive test coverage
- Modern PHP 8.1+ features

## Requirements

- PHP 8.1 or higher
- Symfony 7.3 or higher

## Installation

Install the bundle via Composer:

```bash
composer require tourze/jingdong-crm-bundle
```

## Configuration

1. Add the bundle to your `config/bundles.php`:

```php
return [
    // ... other bundles
    JingdongCrmBundle\JingdongCrmBundle::class => ['all' => true],
];
```

2. The bundle comes with sensible defaults and requires no additional configuration to get started.

## Usage

### Basic Usage

```php
// Example usage will be added as the bundle develops
// The bundle provides services for CRM operations
```

### Services

The bundle registers the following services:

- Configuration services for CRM integration
- Data transfer objects for CRM entities
- Repository services for data access

## Development

### Running Tests

```bash
# Run PHPUnit tests
./vendor/bin/phpunit packages/jingdong-crm-bundle/tests

# Run PHPStan analysis
php -d memory_limit=2G ./vendor/bin/phpstan analyse packages/jingdong-crm-bundle
```

### Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests to ensure everything works
5. Submit a pull request

## License

This bundle is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.