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
 * 销售机会阶段枚举
 */
enum OpportunityStageEnum: string implements Labelable, Itemable, Selectable, BadgeInterface
{
    use ItemTrait;
    use SelectTrait;

    case IDENTIFY_NEEDS = 'identify_needs';
    case SOLUTION_DESIGN = 'solution_design';
    case BUSINESS_NEGOTIATION = 'business_negotiation';
    case CONTRACT_SIGNING = 'contract_signing';
    case CLOSED_WON = 'closed_won';
    case CLOSED_LOST = 'closed_lost';

    public function getLabel(): string
    {
        return match ($this) {
            self::IDENTIFY_NEEDS => '识别需求',
            self::SOLUTION_DESIGN => '方案制作',
            self::BUSINESS_NEGOTIATION => '商务谈判',
            self::CONTRACT_SIGNING => '合同签署',
            self::CLOSED_WON => '已成交',
            self::CLOSED_LOST => '已关闭',
        };
    }

    public function getBadge(): string
    {
        return match ($this) {
            self::IDENTIFY_NEEDS => 'info',
            self::SOLUTION_DESIGN => 'primary',
            self::BUSINESS_NEGOTIATION => 'warning',
            self::CONTRACT_SIGNING => 'light',
            self::CLOSED_WON => 'success',
            self::CLOSED_LOST => 'danger',
        };
    }
}
