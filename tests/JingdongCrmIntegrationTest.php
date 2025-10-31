<?php

namespace JingdongCrmBundle\Tests;

use JingdongCrmBundle\DependencyInjection\JingdongCrmExtension;
use JingdongCrmBundle\JingdongCrmBundle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;

/**
 * @internal
 */
#[CoversClass(JingdongCrmBundle::class)]
#[RunTestsInSeparateProcesses]
final class JingdongCrmIntegrationTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 集成测试的设置逻辑
    }

    /**
     * 测试Extension是否能正常加载
     */
    public function testExtensionLoad(): void
    {
        $extension = new JingdongCrmExtension();
        $container = new ContainerBuilder();

        // 设置基本参数
        $container->setParameter('kernel.bundles', []);
        $container->setParameter('kernel.debug', true);
        $container->setParameter('kernel.environment', 'test');

        // 测试Extension能正常加载
        $extension->load([], $container);
        $this->assertInstanceOf(ContainerBuilder::class, $container);
    }

    /**
     * 测试Extension的别名
     */
    public function testExtensionGetAlias(): void
    {
        $extension = new JingdongCrmExtension();
        $this->assertEquals('jingdong_crm', $extension->getAlias());
    }

    /**
     * 测试Bundle的基本功能
     */
    public function testBundleBasicFunctionality(): void
    {
        $reflection = new \ReflectionClass(JingdongCrmBundle::class);
        $bundle = $reflection->newInstance();
        $this->assertEquals('JingdongCrmBundle', $bundle->getName());

        // 验证Bundle能正确创建Extension
        $extension = $bundle->getContainerExtension();
        $this->assertInstanceOf(JingdongCrmExtension::class, $extension);
    }
}
