<?php

namespace JingdongCrmBundle\Tests\Enum;

use JingdongCrmBundle\Enum\CustomerTypeEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(CustomerTypeEnum::class)]
final class CustomerTypeEnumTest extends AbstractEnumTestCase
{
    #[TestWith([CustomerTypeEnum::INDIVIDUAL, 'individual', '个人', 'info'])]
    #[TestWith([CustomerTypeEnum::ENTERPRISE, 'enterprise', '企业', 'primary'])]
    public function testEnumValueLabelAndBadge(CustomerTypeEnum $enum, string $expectedValue, string $expectedLabel, string $expectedBadge): void
    {
        $this->assertSame($expectedValue, $enum->value);
        $this->assertSame($expectedLabel, $enum->getLabel());
        $this->assertSame($expectedBadge, $enum->getBadge());
    }

    public function testAllCasesExist(): void
    {
        $cases = CustomerTypeEnum::cases();

        $this->assertCount(2, $cases);
        $this->assertContains(CustomerTypeEnum::INDIVIDUAL, $cases);
        $this->assertContains(CustomerTypeEnum::ENTERPRISE, $cases);
    }

    #[TestWith(['individual', CustomerTypeEnum::INDIVIDUAL])]
    #[TestWith(['enterprise', CustomerTypeEnum::ENTERPRISE])]
    public function testFromReturnsCorrectEnum(string $value, CustomerTypeEnum $expectedEnum): void
    {
        $this->assertSame($expectedEnum, CustomerTypeEnum::from($value));
    }

    #[TestWith(['invalid_value'])]
    #[TestWith([''])]
    #[TestWith(['null'])]
    #[TestWith(['unknown'])]
    public function testFromThrowsValueErrorWithInvalidValue(string $invalidValue): void
    {
        $this->expectException(\ValueError::class);
        CustomerTypeEnum::from($invalidValue);
    }

    #[TestWith(['individual', CustomerTypeEnum::INDIVIDUAL])]
    #[TestWith(['enterprise', CustomerTypeEnum::ENTERPRISE])]
    public function testTryFromReturnsEnumWithValidValue(string $value, CustomerTypeEnum $expectedEnum): void
    {
        $this->assertSame($expectedEnum, CustomerTypeEnum::tryFrom($value));
    }

    #[TestWith(['invalid_value'])]
    #[TestWith([''])]
    #[TestWith(['null'])]
    #[TestWith(['unknown'])]
    public function testTryFromReturnsNullWithInvalidValue(string $invalidValue): void
    {
        $this->assertNull(CustomerTypeEnum::tryFrom($invalidValue));
    }

    public function testValuesAreUnique(): void
    {
        $values = array_map(fn (CustomerTypeEnum $case) => $case->value, CustomerTypeEnum::cases());
        $uniqueValues = array_unique($values);

        $this->assertCount(count($values), $uniqueValues, 'All enum values must be unique');
    }

    public function testLabelsAreUnique(): void
    {
        $labels = array_map(fn (CustomerTypeEnum $case) => $case->getLabel(), CustomerTypeEnum::cases());
        $uniqueLabels = array_unique($labels);

        $this->assertCount(count($labels), $uniqueLabels, 'All enum labels must be unique');
    }

    public function testBadgesAreValid(): void
    {
        $validBadges = ['success', 'warning', 'danger', 'info', 'primary', 'secondary', 'light', 'dark'];

        foreach (CustomerTypeEnum::cases() as $case) {
            $this->assertContains($case->getBadge(), $validBadges, "Badge '{$case->getBadge()}' is not valid");
        }
    }

    public function testToArray(): void
    {
        $result = CustomerTypeEnum::INDIVIDUAL->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'individual', 'label' => '个人'], $result);

        $result = CustomerTypeEnum::ENTERPRISE->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'enterprise', 'label' => '企业'], $result);
    }

    public function testGenOptions(): void
    {
        $options = CustomerTypeEnum::genOptions();
        $this->assertIsArray($options);
        $this->assertCount(2, $options);

        foreach ($options as $item) {
            $this->assertArrayHasKey('value', $item);
            $this->assertArrayHasKey('label', $item);
            $this->assertArrayHasKey('text', $item);
            $this->assertArrayHasKey('name', $item);
        }

        // Test specific values
        $this->assertEquals([
            'label' => '个人',
            'text' => '个人',
            'value' => 'individual',
            'name' => '个人',
        ], $options[0]);

        $this->assertEquals([
            'label' => '企业',
            'text' => '企业',
            'value' => 'enterprise',
            'name' => '企业',
        ], $options[1]);
    }
}
