<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Tests;

use JingdongCrmBundle\JingdongCrmBundle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(JingdongCrmBundle::class)]
#[RunTestsInSeparateProcesses]
final class JingdongCrmBundleTest extends AbstractBundleTestCase
{
}
