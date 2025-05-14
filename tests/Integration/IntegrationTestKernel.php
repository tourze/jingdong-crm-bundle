<?php

namespace JingdongCrmBundle\Tests\Integration;

use JingdongCrmBundle\JingdongCrmBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class IntegrationTestKernel extends Kernel
{
    use MicroKernelTrait;

    private array $configs = [];

    public function __construct(string $environment = 'test', bool $debug = true, array $configs = [])
    {
        parent::__construct($environment, $debug);
        
        $this->configs = $configs;
    }

    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new JingdongCrmBundle(),
        ];
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->loadFromExtension('framework', [
            'test' => true,
            'secret' => 'test-secret',
            // 添加必要的框架配置以避免废弃警告
            'http_method_override' => false,
            'handle_all_throwables' => true,
            'php_errors' => [
                'log' => true,
            ],
            'validation' => [
                'email_validation_mode' => 'html5',
            ],
            'uid' => [
                'default_uuid_version' => 7,
                'time_based_uuid_version' => 7,
            ],
        ]);

        // 添加自定义配置
        foreach ($this->configs as $name => $config) {
            $container->loadFromExtension($name, $config);
        }
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        // 可以在此添加路由配置
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/jingdong-crm-bundle/cache';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/jingdong-crm-bundle/log';
    }
} 