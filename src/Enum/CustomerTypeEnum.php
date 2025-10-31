<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Enum;

use Tourze\EnumExtra\BadgeInterface;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * 客户类型枚举
 */
enum CustomerTypeEnum: string implements Labelable, Itemable, Selectable, BadgeInterface
{
    use ItemTrait;
    use SelectTrait;

    case INDIVIDUAL = 'individual';
    case ENTERPRISE = 'enterprise';

    public function getLabel(): string
    {
        return match ($this) {
            self::INDIVIDUAL => '个人',
            self::ENTERPRISE => '企业',
        };
    }

    public function getBadge(): string
    {
        return match ($this) {
            self::INDIVIDUAL => 'info',
            self::ENTERPRISE => 'primary',
        };
    }
}
