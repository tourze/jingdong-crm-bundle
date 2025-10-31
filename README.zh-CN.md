# jingdong-crm-bundle

[![PHP Version](https://img.shields.io/badge/php-%5E8.1-blue.svg)](https://php.net)
[![Symfony Version](https://img.shields.io/badge/symfony-%5E7.3-green.svg)](https://symfony.com)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)
[![Build Status](https://img.shields.io/badge/build-passing-brightgreen.svg)](https://github.com/tourze/php-monorepo)
[![Code Coverage](https://img.shields.io/badge/coverage-100%25-brightgreen.svg)](https://github.com/tourze/php-monorepo)

[English](README.md) | [中文](README.zh-CN.md)

京东CRM系统集成Bundle，提供京东客户关系管理功能。

## 特性

- 京东CRM系统集成
- 兼容 Symfony 7.3+
- 完整的依赖注入支持
- 全面的测试覆盖
- 现代化的 PHP 8.1+ 特性

## 系统要求

- PHP 8.1 或更高版本
- Symfony 7.3 或更高版本

## 安装

通过 Composer 安装 Bundle：

```bash
composer require tourze/jingdong-crm-bundle
```

## 配置

1. 将 Bundle 添加到您的 `config/bundles.php`：

```php
return [
    // ... 其他bundles
    JingdongCrmBundle\JingdongCrmBundle::class => ['all' => true],
];
```

2. Bundle 已包含合理的默认配置，无需额外配置即可开始使用。

## 使用方法

### 基本用法

```php
// 随着 Bundle 开发将添加使用示例
// Bundle 提供用于 CRM 操作的服务
```

### 服务

Bundle 注册以下服务：

- CRM 集成的配置服务
- CRM 实体的数据传输对象
- 数据访问的仓库服务

## 开发

### 运行测试

```bash
# 运行 PHPUnit 测试
./vendor/bin/phpunit packages/jingdong-crm-bundle/tests

# 运行 PHPStan 分析
php -d memory_limit=2G ./vendor/bin/phpstan analyse packages/jingdong-crm-bundle
```

### 贡献

1. Fork 仓库
2. 创建功能分支
3. 进行更改
4. 运行测试确保一切正常
5. 提交拉取请求

## 许可证

该 Bundle 基于 MIT 许可证发布。详情请参阅 [LICENSE](LICENSE) 文件。