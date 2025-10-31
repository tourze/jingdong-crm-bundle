<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Enum;

use Tourze\EnumExtra\BadgeInterface;
use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum ProductStatus: string implements Labelable, Itemable, Selectable, BadgeInterface
{
    use ItemTrait;
    use SelectTrait;

    case ON_SALE = 'on_sale';
    case OFF_SHELF = 'off_shelf';
    case OUT_OF_STOCK = 'out_of_stock';

    public function getLabel(): string
    {
        return match ($this) {
            self::ON_SALE => '在售',
            self::OFF_SHELF => '下架',
            self::OUT_OF_STOCK => '缺货',
        };
    }

    public function label(): string
    {
        return $this->getLabel();
    }

    public function isOnSale(): bool
    {
        return self::ON_SALE === $this;
    }

    public function isOffShelf(): bool
    {
        return self::OFF_SHELF === $this;
    }

    public function isOutOfStock(): bool
    {
        return self::OUT_OF_STOCK === $this;
    }

    public function getBadge(): string
    {
        return match ($this) {
            self::ON_SALE => 'success',
            self::OFF_SHELF => 'secondary',
            self::OUT_OF_STOCK => 'warning',
        };
    }
}
