<?php

namespace JingdongCrmBundle\Tests\Enum;

use JingdongCrmBundle\Enum\ContactStatusEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(ContactStatusEnum::class)]
final class ContactStatusEnumTest extends AbstractEnumTestCase
{
    #[TestWith([ContactStatusEnum::ACTIVE, 'active', '活跃'])]
    #[TestWith([ContactStatusEnum::INACTIVE, 'inactive', '非活跃'])]
    public function testEnumValueAndLabel(ContactStatusEnum $enum, string $expectedValue, string $expectedLabel): void
    {
        $this->assertSame($expectedValue, $enum->value);
        $this->assertSame($expectedLabel, $enum->getLabel());
    }

    public function testAllCasesExist(): void
    {
        $cases = ContactStatusEnum::cases();

        $this->assertCount(2, $cases);
        $this->assertContains(ContactStatusEnum::ACTIVE, $cases);
        $this->assertContains(ContactStatusEnum::INACTIVE, $cases);
    }

    #[TestWith(['active', ContactStatusEnum::ACTIVE])]
    #[TestWith(['inactive', ContactStatusEnum::INACTIVE])]
    public function testFromReturnsCorrectEnum(string $value, ContactStatusEnum $expectedEnum): void
    {
        $this->assertSame($expectedEnum, ContactStatusEnum::from($value));
    }

    #[TestWith(['invalid_value'])]
    #[TestWith([''])]
    #[TestWith(['null'])]
    #[TestWith(['unknown'])]
    public function testFromThrowsValueErrorWithInvalidValue(string $invalidValue): void
    {
        $this->expectException(\ValueError::class);
        ContactStatusEnum::from($invalidValue);
    }

    #[TestWith(['active', ContactStatusEnum::ACTIVE])]
    #[TestWith(['inactive', ContactStatusEnum::INACTIVE])]
    public function testTryFromReturnsEnumWithValidValue(string $value, ContactStatusEnum $expectedEnum): void
    {
        $this->assertSame($expectedEnum, ContactStatusEnum::tryFrom($value));
    }

    #[TestWith(['invalid_value'])]
    #[TestWith([''])]
    #[TestWith(['null'])]
    #[TestWith(['unknown'])]
    public function testTryFromReturnsNullWithInvalidValue(string $invalidValue): void
    {
        $this->assertNull(ContactStatusEnum::tryFrom($invalidValue));
    }

    public function testValuesAreUnique(): void
    {
        $values = array_map(fn (ContactStatusEnum $case) => $case->value, ContactStatusEnum::cases());
        $uniqueValues = array_unique($values);

        $this->assertCount(count($values), $uniqueValues, 'All enum values must be unique');
    }

    public function testLabelsAreUnique(): void
    {
        $labels = array_map(fn (ContactStatusEnum $case) => $case->getLabel(), ContactStatusEnum::cases());
        $uniqueLabels = array_unique($labels);

        $this->assertCount(count($labels), $uniqueLabels, 'All enum labels must be unique');
    }

    public function testToArray(): void
    {
        $result = ContactStatusEnum::ACTIVE->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'active', 'label' => '活跃'], $result);

        $result = ContactStatusEnum::INACTIVE->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'inactive', 'label' => '非活跃'], $result);
    }

    public function testGenOptions(): void
    {
        $options = ContactStatusEnum::genOptions();
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
            'label' => '活跃',
            'text' => '活跃',
            'value' => 'active',
            'name' => '活跃',
        ], $options[0]);

        $this->assertEquals([
            'label' => '非活跃',
            'text' => '非活跃',
            'value' => 'inactive',
            'name' => '非活跃',
        ], $options[1]);
    }
}
