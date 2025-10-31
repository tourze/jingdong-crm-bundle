<?php

namespace JingdongCrmBundle\Tests\Enum;

use JingdongCrmBundle\Enum\OpportunityStatusEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(OpportunityStatusEnum::class)]
final class OpportunityStatusEnumTest extends AbstractEnumTestCase
{
    #[TestWith([OpportunityStatusEnum::ACTIVE, 'active', '进行中', 'primary'])]
    #[TestWith([OpportunityStatusEnum::WON, 'won', '赢单', 'success'])]
    #[TestWith([OpportunityStatusEnum::LOST, 'lost', '败单', 'danger'])]
    public function testEnumValueLabelAndBadge(OpportunityStatusEnum $enum, string $expectedValue, string $expectedLabel, string $expectedBadge): void
    {
        $this->assertSame($expectedValue, $enum->value);
        $this->assertSame($expectedLabel, $enum->getLabel());
        $this->assertSame($expectedBadge, $enum->getBadge());
    }

    public function testAllCasesExist(): void
    {
        $cases = OpportunityStatusEnum::cases();

        $this->assertCount(3, $cases);
        $this->assertContains(OpportunityStatusEnum::ACTIVE, $cases);
        $this->assertContains(OpportunityStatusEnum::WON, $cases);
        $this->assertContains(OpportunityStatusEnum::LOST, $cases);
    }

    #[TestWith(['active', OpportunityStatusEnum::ACTIVE])]
    #[TestWith(['won', OpportunityStatusEnum::WON])]
    #[TestWith(['lost', OpportunityStatusEnum::LOST])]
    public function testFromReturnsCorrectEnum(string $value, OpportunityStatusEnum $expectedEnum): void
    {
        $this->assertSame($expectedEnum, OpportunityStatusEnum::from($value));
    }

    #[TestWith(['invalid_value'])]
    #[TestWith([''])]
    #[TestWith(['null'])]
    #[TestWith(['unknown'])]
    public function testFromThrowsValueErrorWithInvalidValue(string $invalidValue): void
    {
        $this->expectException(\ValueError::class);
        OpportunityStatusEnum::from($invalidValue);
    }

    #[TestWith(['active', OpportunityStatusEnum::ACTIVE])]
    #[TestWith(['won', OpportunityStatusEnum::WON])]
    #[TestWith(['lost', OpportunityStatusEnum::LOST])]
    public function testTryFromReturnsEnumWithValidValue(string $value, OpportunityStatusEnum $expectedEnum): void
    {
        $this->assertSame($expectedEnum, OpportunityStatusEnum::tryFrom($value));
    }

    #[TestWith(['invalid_value'])]
    #[TestWith([''])]
    #[TestWith(['null'])]
    #[TestWith(['unknown'])]
    public function testTryFromReturnsNullWithInvalidValue(string $invalidValue): void
    {
        $this->assertNull(OpportunityStatusEnum::tryFrom($invalidValue));
    }

    public function testValuesAreUnique(): void
    {
        $values = array_map(fn (OpportunityStatusEnum $case) => $case->value, OpportunityStatusEnum::cases());
        $uniqueValues = array_unique($values);

        $this->assertCount(count($values), $uniqueValues, 'All enum values must be unique');
    }

    public function testLabelsAreUnique(): void
    {
        $labels = array_map(fn (OpportunityStatusEnum $case) => $case->getLabel(), OpportunityStatusEnum::cases());
        $uniqueLabels = array_unique($labels);

        $this->assertCount(count($labels), $uniqueLabels, 'All enum labels must be unique');
    }

    public function testBadgesAreValid(): void
    {
        $validBadges = ['success', 'warning', 'danger', 'info', 'primary', 'secondary', 'light', 'dark'];

        foreach (OpportunityStatusEnum::cases() as $case) {
            $this->assertContains($case->getBadge(), $validBadges, "Badge '{$case->getBadge()}' is not valid");
        }
    }

    public function testToArray(): void
    {
        $result = OpportunityStatusEnum::ACTIVE->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'active', 'label' => '进行中'], $result);

        $result = OpportunityStatusEnum::WON->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'won', 'label' => '赢单'], $result);

        $result = OpportunityStatusEnum::LOST->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'lost', 'label' => '败单'], $result);
    }

    public function testGenOptions(): void
    {
        $options = OpportunityStatusEnum::genOptions();
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
            'label' => '进行中',
            'text' => '进行中',
            'value' => 'active',
            'name' => '进行中',
        ], $options[0]);

        $this->assertEquals([
            'label' => '赢单',
            'text' => '赢单',
            'value' => 'won',
            'name' => '赢单',
        ], $options[1]);

        $this->assertEquals([
            'label' => '败单',
            'text' => '败单',
            'value' => 'lost',
            'name' => '败单',
        ], $options[2]);
    }
}
