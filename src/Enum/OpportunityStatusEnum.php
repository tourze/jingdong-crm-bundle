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
 * 销售机会状态枚举
 */
enum OpportunityStatusEnum: string implements Labelable, Itemable, Selectable, BadgeInterface
{
    use ItemTrait;
    use SelectTrait;

    case ACTIVE = 'active';
    case WON = 'won';
    case LOST = 'lost';

    public function getLabel(): string
    {
        return match ($this) {
            self::ACTIVE => '进行中',
            self::WON => '赢单',
            self::LOST => '败单',
        };
    }

    public function getBadge(): string
    {
        return match ($this) {
            self::ACTIVE => 'primary',
            self::WON => 'success',
            self::LOST => 'danger',
        };
    }
}
