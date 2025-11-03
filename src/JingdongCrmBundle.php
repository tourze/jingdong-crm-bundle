<?php

declare(strict_types=1);

namespace JingdongCrmBundle;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use EasyCorp\Bundle\EasyAdminBundle\EasyAdminBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\DoctrineSnowflakeBundle\DoctrineSnowflakeBundle;
use Tourze\DoctrineTimestampBundle\DoctrineTimestampBundle;
use Tourze\EasyAdminEnumFieldBundle\EasyAdminEnumFieldBundle;
use Tourze\EasyAdminMenuBundle\EasyAdminMenuBundle;

class JingdongCrmBundle extends Bundle implements BundleDependencyInterface
{
    /**
     * @return array<class-string<Bundle>, array<string, bool>>
     */
    public static function getBundleDependencies(): array
    {
        return [
            DoctrineBundle::class => ['all' => true],
            DoctrineSnowflakeBundle::class => ['all' => true],
            DoctrineTimestampBundle::class => ['all' => true],
            EasyAdminBundle::class => ['all' => true],
            EasyAdminEnumFieldBundle::class => ['all' => true],
            TwigBundle::class => ['all' => true],
            EasyAdminMenuBundle::class => ['all' => true],
        ];
    }
}
