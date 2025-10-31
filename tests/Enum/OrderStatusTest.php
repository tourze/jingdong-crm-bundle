<?php

namespace JingdongCrmBundle\Tests\Enum;

use JingdongCrmBundle\Enum\OrderStatus;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(OrderStatus::class)]
final class OrderStatusTest extends AbstractEnumTestCase
{
    #[TestWith([OrderStatus::PENDING_PAYMENT, 'pending_payment', '待支付', 'warning'])]
    #[TestWith([OrderStatus::PAID, 'paid', '已支付', 'info'])]
    #[TestWith([OrderStatus::SHIPPING, 'shipping', '配送中', 'primary'])]
    #[TestWith([OrderStatus::COMPLETED, 'completed', '已完成', 'success'])]
    #[TestWith([OrderStatus::CANCELLED, 'cancelled', '已取消', 'secondary'])]
    #[TestWith([OrderStatus::REFUNDED, 'refunded', '已退款', 'danger'])]
    public function testEnumValueLabelAndBadge(OrderStatus $enum, string $expectedValue, string $expectedLabel, string $expectedBadge): void
    {
        $this->assertSame($expectedValue, $enum->value);
        $this->assertSame($expectedLabel, $enum->getLabel());
        $this->assertSame($expectedBadge, $enum->getBadge());
    }

    public function testAllCasesExist(): void
    {
        $cases = OrderStatus::cases();

        $this->assertCount(6, $cases);
        $this->assertContains(OrderStatus::PENDING_PAYMENT, $cases);
        $this->assertContains(OrderStatus::PAID, $cases);
        $this->assertContains(OrderStatus::SHIPPING, $cases);
        $this->assertContains(OrderStatus::COMPLETED, $cases);
        $this->assertContains(OrderStatus::CANCELLED, $cases);
        $this->assertContains(OrderStatus::REFUNDED, $cases);
    }

    #[TestWith(['pending_payment', OrderStatus::PENDING_PAYMENT])]
    #[TestWith(['paid', OrderStatus::PAID])]
    #[TestWith(['shipping', OrderStatus::SHIPPING])]
    #[TestWith(['completed', OrderStatus::COMPLETED])]
    #[TestWith(['cancelled', OrderStatus::CANCELLED])]
    #[TestWith(['refunded', OrderStatus::REFUNDED])]
    public function testFromReturnsCorrectEnum(string $value, OrderStatus $expectedEnum): void
    {
        $this->assertSame($expectedEnum, OrderStatus::from($value));
    }

    #[TestWith(['invalid_value'])]
    #[TestWith([''])]
    #[TestWith(['null'])]
    #[TestWith(['unknown'])]
    public function testFromThrowsValueErrorWithInvalidValue(string $invalidValue): void
    {
        $this->expectException(\ValueError::class);
        OrderStatus::from($invalidValue);
    }

    #[TestWith(['pending_payment', OrderStatus::PENDING_PAYMENT])]
    #[TestWith(['paid', OrderStatus::PAID])]
    #[TestWith(['shipping', OrderStatus::SHIPPING])]
    #[TestWith(['completed', OrderStatus::COMPLETED])]
    #[TestWith(['cancelled', OrderStatus::CANCELLED])]
    #[TestWith(['refunded', OrderStatus::REFUNDED])]
    public function testTryFromReturnsEnumWithValidValue(string $value, OrderStatus $expectedEnum): void
    {
        $this->assertSame($expectedEnum, OrderStatus::tryFrom($value));
    }

    #[TestWith(['invalid_value'])]
    #[TestWith([''])]
    #[TestWith(['null'])]
    #[TestWith(['unknown'])]
    public function testTryFromReturnsNullWithInvalidValue(string $invalidValue): void
    {
        $this->assertNull(OrderStatus::tryFrom($invalidValue));
    }

    public function testValuesAreUnique(): void
    {
        $values = array_map(fn (OrderStatus $case) => $case->value, OrderStatus::cases());
        $uniqueValues = array_unique($values);

        $this->assertCount(count($values), $uniqueValues, 'All enum values must be unique');
    }

    public function testLabelsAreUnique(): void
    {
        $labels = array_map(fn (OrderStatus $case) => $case->getLabel(), OrderStatus::cases());
        $uniqueLabels = array_unique($labels);

        $this->assertCount(count($labels), $uniqueLabels, 'All enum labels must be unique');
    }

    public function testBadgesAreValid(): void
    {
        $validBadges = ['success', 'warning', 'danger', 'info', 'primary', 'secondary', 'light', 'dark'];

        foreach (OrderStatus::cases() as $case) {
            $this->assertContains($case->getBadge(), $validBadges, "Badge '{$case->getBadge()}' is not valid");
        }
    }

    public function testToArray(): void
    {
        $result = OrderStatus::PENDING_PAYMENT->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'pending_payment', 'label' => '待支付'], $result);

        $result = OrderStatus::PAID->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'paid', 'label' => '已支付'], $result);

        $result = OrderStatus::SHIPPING->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'shipping', 'label' => '配送中'], $result);

        $result = OrderStatus::COMPLETED->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'completed', 'label' => '已完成'], $result);

        $result = OrderStatus::CANCELLED->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'cancelled', 'label' => '已取消'], $result);

        $result = OrderStatus::REFUNDED->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'refunded', 'label' => '已退款'], $result);
    }

    public function testGenOptions(): void
    {
        $options = OrderStatus::genOptions();
        $this->assertIsArray($options);
        $this->assertCount(6, $options);

        foreach ($options as $item) {
            $this->assertArrayHasKey('value', $item);
            $this->assertArrayHasKey('label', $item);
            $this->assertArrayHasKey('text', $item);
            $this->assertArrayHasKey('name', $item);
        }

        // Test specific values
        $this->assertEquals([
            'label' => '待支付',
            'text' => '待支付',
            'value' => 'pending_payment',
            'name' => '待支付',
        ], $options[0]);

        $this->assertEquals([
            'label' => '已支付',
            'text' => '已支付',
            'value' => 'paid',
            'name' => '已支付',
        ], $options[1]);

        $this->assertEquals([
            'label' => '配送中',
            'text' => '配送中',
            'value' => 'shipping',
            'name' => '配送中',
        ], $options[2]);

        $this->assertEquals([
            'label' => '已完成',
            'text' => '已完成',
            'value' => 'completed',
            'name' => '已完成',
        ], $options[3]);

        $this->assertEquals([
            'label' => '已取消',
            'text' => '已取消',
            'value' => 'cancelled',
            'name' => '已取消',
        ], $options[4]);

        $this->assertEquals([
            'label' => '已退款',
            'text' => '已退款',
            'value' => 'refunded',
            'name' => '已退款',
        ], $options[5]);
    }
}
