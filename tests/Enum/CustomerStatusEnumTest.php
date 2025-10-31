<?php

namespace JingdongCrmBundle\Tests\Enum;

use JingdongCrmBundle\Enum\CustomerStatusEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(CustomerStatusEnum::class)]
final class CustomerStatusEnumTest extends AbstractEnumTestCase
{
    #[TestWith([CustomerStatusEnum::ACTIVE, 'active', '活跃', 'success'])]
    #[TestWith([CustomerStatusEnum::SUSPENDED, 'suspended', '暂停', 'warning'])]
    #[TestWith([CustomerStatusEnum::CLOSED, 'closed', '关闭', 'danger'])]
    public function testEnumValueLabelAndBadge(CustomerStatusEnum $enum, string $expectedValue, string $expectedLabel, string $expectedBadge): void
    {
        $this->assertSame($expectedValue, $enum->value);
        $this->assertSame($expectedLabel, $enum->getLabel());
        $this->assertSame($expectedBadge, $enum->getBadge());
    }

    public function testAllCasesExist(): void
    {
        $cases = CustomerStatusEnum::cases();

        $this->assertCount(3, $cases);
        $this->assertContains(CustomerStatusEnum::ACTIVE, $cases);
        $this->assertContains(CustomerStatusEnum::SUSPENDED, $cases);
        $this->assertContains(CustomerStatusEnum::CLOSED, $cases);
    }

    #[TestWith(['active', CustomerStatusEnum::ACTIVE])]
    #[TestWith(['suspended', CustomerStatusEnum::SUSPENDED])]
    #[TestWith(['closed', CustomerStatusEnum::CLOSED])]
    public function testFromReturnsCorrectEnum(string $value, CustomerStatusEnum $expectedEnum): void
    {
        $this->assertSame($expectedEnum, CustomerStatusEnum::from($value));
    }

    #[TestWith(['invalid_value'])]
    #[TestWith([''])]
    #[TestWith(['null'])]
    #[TestWith(['unknown'])]
    public function testFromThrowsValueErrorWithInvalidValue(string $invalidValue): void
    {
        $this->expectException(\ValueError::class);
        CustomerStatusEnum::from($invalidValue);
    }

    #[TestWith(['active', CustomerStatusEnum::ACTIVE])]
    #[TestWith(['suspended', CustomerStatusEnum::SUSPENDED])]
    #[TestWith(['closed', CustomerStatusEnum::CLOSED])]
    public function testTryFromReturnsEnumWithValidValue(string $value, CustomerStatusEnum $expectedEnum): void
    {
        $this->assertSame($expectedEnum, CustomerStatusEnum::tryFrom($value));
    }

    #[TestWith(['invalid_value'])]
    #[TestWith([''])]
    #[TestWith(['null'])]
    #[TestWith(['unknown'])]
    public function testTryFromReturnsNullWithInvalidValue(string $invalidValue): void
    {
        $this->assertNull(CustomerStatusEnum::tryFrom($invalidValue));
    }

    public function testValuesAreUnique(): void
    {
        $values = array_map(fn (CustomerStatusEnum $case) => $case->value, CustomerStatusEnum::cases());
        $uniqueValues = array_unique($values);

        $this->assertCount(count($values), $uniqueValues, 'All enum values must be unique');
    }

    public function testLabelsAreUnique(): void
    {
        $labels = array_map(fn (CustomerStatusEnum $case) => $case->getLabel(), CustomerStatusEnum::cases());
        $uniqueLabels = array_unique($labels);

        $this->assertCount(count($labels), $uniqueLabels, 'All enum labels must be unique');
    }

    public function testBadgesAreValid(): void
    {
        $validBadges = ['success', 'warning', 'danger', 'info', 'primary', 'secondary', 'light', 'dark'];

        foreach (CustomerStatusEnum::cases() as $case) {
            $this->assertContains($case->getBadge(), $validBadges, "Badge '{$case->getBadge()}' is not valid");
        }
    }

    public function testToArray(): void
    {
        $result = CustomerStatusEnum::ACTIVE->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'active', 'label' => '活跃'], $result);

        $result = CustomerStatusEnum::SUSPENDED->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'suspended', 'label' => '暂停'], $result);

        $result = CustomerStatusEnum::CLOSED->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'closed', 'label' => '关闭'], $result);
    }

    public function testGenOptions(): void
    {
        $options = CustomerStatusEnum::genOptions();
        $this->assertIsArray($options);
        $this->assertCount(3, $options);

        foreach ($options as $item) {
            $this->assertArrayHasKey('value', $item);
            $this->assertArrayHasKey('label', $item);
            $this->assertArrayHasKey('text', $item);
            $this->assertArrayHasKey('name', $item);
        }

        // Test specific values
        $this->assertEquals([
            'label' => '活跃',
            'text' => '活跃',
            'value' => 'active',
            'name' => '活跃',
        ], $options[0]);

        $this->assertEquals([
            'label' => '暂停',
            'text' => '暂停',
            'value' => 'suspended',
            'name' => '暂停',
        ], $options[1]);

        $this->assertEquals([
            'label' => '关闭',
            'text' => '关闭',
            'value' => 'closed',
            'name' => '关闭',
        ], $options[2]);
    }
}
