<?php

namespace JingdongCrmBundle\Tests\Unit;

use JingdongCrmBundle\JingdongCrmBundle;
use PHPUnit\Framework\TestCase;

class JingdongCrmBundleTest extends TestCase
{
    /**
     * 测试Bundle能否正确实例化
     */
    public function testInitialization_success(): void
    {
        $bundle = new JingdongCrmBundle();
        $this->assertInstanceOf(JingdongCrmBundle::class, $bundle);
    }

    /**
     * 测试Bundle的getPath方法返回正确的路径
     */
    public function testGetPath_returnsCorrectPath(): void
    {
        $bundle = new JingdongCrmBundle();
        $path = $bundle->getPath();
        
        // 确保路径指向src目录所在的位置
        $this->assertStringContainsString('jingdong-crm-bundle' . DIRECTORY_SEPARATOR . 'src', $path);
        $this->assertDirectoryExists($path);
    }
} 