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
 * 线索状态枚举
 */
enum LeadStatus: string implements Labelable, Itemable, Selectable, BadgeInterface
{
    use ItemTrait;
    use SelectTrait;

    case NEW = 'new';
    case IN_PROGRESS = 'in_progress';
    case CONVERTED = 'converted';
    case CLOSED = 'closed';

    public function getLabel(): string
    {
        return match ($this) {
            self::NEW => '新建',
            self::IN_PROGRESS => '跟进中',
            self::CONVERTED => '已转化',
            self::CLOSED => '已关闭',
        };
    }

    public function getBadge(): string
    {
        return match ($this) {
            self::NEW => 'info',
            self::IN_PROGRESS => 'warning',
            self::CONVERTED => 'success',
            self::CLOSED => 'secondary',
        };
    }
}
