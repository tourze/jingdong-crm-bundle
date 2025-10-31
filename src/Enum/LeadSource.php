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
 * 线索来源枚举
 */
enum LeadSource: string implements Labelable, Itemable, Selectable, BadgeInterface
{
    use ItemTrait;
    use SelectTrait;

    case WEBSITE = 'website';
    case PHONE = 'phone';
    case ADVERTISEMENT = 'advertisement';
    case REFERRAL = 'referral';
    case OTHER = 'other';

    public function getLabel(): string
    {
        return match ($this) {
            self::WEBSITE => '网站',
            self::PHONE => '电话',
            self::ADVERTISEMENT => '广告',
            self::REFERRAL => '推荐',
            self::OTHER => '其他',
        };
    }

    public function getBadge(): string
    {
        return match ($this) {
            self::WEBSITE => 'primary',
            self::PHONE => 'info',
            self::ADVERTISEMENT => 'warning',
            self::REFERRAL => 'success',
            self::OTHER => 'secondary',
        };
    }
}
