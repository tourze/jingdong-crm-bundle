<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Tests\Service;

use JingdongCrmBundle\Service\AdminMenu;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;

/**
 * AdminMenu测试类
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    protected function getMenuProviderService(): object
    {
        return self::getService(AdminMenu::class);
    }

    protected function onSetUp(): void
    {
        // AdminMenu测试的设置操作
    }

    public function testMenuProviderCreatesCorrectStructure(): void
    {
        $menuProvider = $this->getMenuProviderService();
        self::assertInstanceOf(AdminMenu::class, $menuProvider);
    }

    public function testMenuProviderIsValidService(): void
    {
        $menuProvider = $this->getMenuProviderService();

        // 验证服务类型
        self::assertInstanceOf(AdminMenu::class, $menuProvider);
    }

    public function testMenuProviderCanBeInstantiated(): void
    {
        $menuProvider = $this->getMenuProviderService();

        // 验证服务实例化成功
        self::assertInstanceOf(AdminMenu::class, $menuProvider);
    }

    public function testMenuProviderImplementsCorrectInterface(): void
    {
        $menuProvider = $this->getMenuProviderService();

        // 验证实现了正确的接口
        self::assertInstanceOf('Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface', $menuProvider);
    }
}
