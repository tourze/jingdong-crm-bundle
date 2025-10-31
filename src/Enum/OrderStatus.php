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
 * 订单状态枚举
 */
enum OrderStatus: string implements Labelable, Itemable, Selectable, BadgeInterface
{
    use ItemTrait;
    use SelectTrait;

    case PENDING_PAYMENT = 'pending_payment';
    case PAID = 'paid';
    case SHIPPING = 'shipping';
    case COMPLETED = 'completed';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING_PAYMENT => '待支付',
            self::PAID => '已支付',
            self::SHIPPING => '配送中',
            self::COMPLETED => '已完成',
            self::CANCELLED => '已取消',
            self::REFUNDED => '已退款',
        };
    }

    public function getBadge(): string
    {
        return match ($this) {
            self::PENDING_PAYMENT => 'warning',
            self::PAID => 'info',
            self::SHIPPING => 'primary',
            self::COMPLETED => 'success',
            self::CANCELLED => 'secondary',
            self::REFUNDED => 'danger',
        };
    }
}
