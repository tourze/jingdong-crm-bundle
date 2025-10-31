<?php

namespace JingdongCrmBundle\Tests\Enum;

use JingdongCrmBundle\Enum\OpportunityStageEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(OpportunityStageEnum::class)]
final class OpportunityStageEnumTest extends AbstractEnumTestCase
{
    #[TestWith([OpportunityStageEnum::IDENTIFY_NEEDS, 'identify_needs', '识别需求', 'info'])]
    #[TestWith([OpportunityStageEnum::SOLUTION_DESIGN, 'solution_design', '方案制作', 'primary'])]
    #[TestWith([OpportunityStageEnum::BUSINESS_NEGOTIATION, 'business_negotiation', '商务谈判', 'warning'])]
    #[TestWith([OpportunityStageEnum::CONTRACT_SIGNING, 'contract_signing', '合同签署', 'light'])]
    #[TestWith([OpportunityStageEnum::CLOSED_WON, 'closed_won', '已成交', 'success'])]
    #[TestWith([OpportunityStageEnum::CLOSED_LOST, 'closed_lost', '已关闭', 'danger'])]
    public function testEnumValueLabelAndBadge(OpportunityStageEnum $enum, string $expectedValue, string $expectedLabel, string $expectedBadge): void
    {
        $this->assertSame($expectedValue, $enum->value);
        $this->assertSame($expectedLabel, $enum->getLabel());
        $this->assertSame($expectedBadge, $enum->getBadge());
    }

    public function testAllCasesExist(): void
    {
        $cases = OpportunityStageEnum::cases();

        $this->assertCount(6, $cases);
        $this->assertContains(OpportunityStageEnum::IDENTIFY_NEEDS, $cases);
        $this->assertContains(OpportunityStageEnum::SOLUTION_DESIGN, $cases);
        $this->assertContains(OpportunityStageEnum::BUSINESS_NEGOTIATION, $cases);
        $this->assertContains(OpportunityStageEnum::CONTRACT_SIGNING, $cases);
        $this->assertContains(OpportunityStageEnum::CLOSED_WON, $cases);
        $this->assertContains(OpportunityStageEnum::CLOSED_LOST, $cases);
    }

    #[TestWith(['identify_needs', OpportunityStageEnum::IDENTIFY_NEEDS])]
    #[TestWith(['solution_design', OpportunityStageEnum::SOLUTION_DESIGN])]
    #[TestWith(['business_negotiation', OpportunityStageEnum::BUSINESS_NEGOTIATION])]
    #[TestWith(['contract_signing', OpportunityStageEnum::CONTRACT_SIGNING])]
    #[TestWith(['closed_won', OpportunityStageEnum::CLOSED_WON])]
    #[TestWith(['closed_lost', OpportunityStageEnum::CLOSED_LOST])]
    public function testFromReturnsCorrectEnum(string $value, OpportunityStageEnum $expectedEnum): void
    {
        $this->assertSame($expectedEnum, OpportunityStageEnum::from($value));
    }

    #[TestWith(['invalid_value'])]
    #[TestWith([''])]
    #[TestWith(['null'])]
    #[TestWith(['unknown'])]
    public function testFromThrowsValueErrorWithInvalidValue(string $invalidValue): void
    {
        $this->expectException(\ValueError::class);
        OpportunityStageEnum::from($invalidValue);
    }

    #[TestWith(['identify_needs', OpportunityStageEnum::IDENTIFY_NEEDS])]
    #[TestWith(['solution_design', OpportunityStageEnum::SOLUTION_DESIGN])]
    #[TestWith(['business_negotiation', OpportunityStageEnum::BUSINESS_NEGOTIATION])]
    #[TestWith(['contract_signing', OpportunityStageEnum::CONTRACT_SIGNING])]
    #[TestWith(['closed_won', OpportunityStageEnum::CLOSED_WON])]
    #[TestWith(['closed_lost', OpportunityStageEnum::CLOSED_LOST])]
    public function testTryFromReturnsEnumWithValidValue(string $value, OpportunityStageEnum $expectedEnum): void
    {
        $this->assertSame($expectedEnum, OpportunityStageEnum::tryFrom($value));
    }

    #[TestWith(['invalid_value'])]
    #[TestWith([''])]
    #[TestWith(['null'])]
    #[TestWith(['unknown'])]
    public function testTryFromReturnsNullWithInvalidValue(string $invalidValue): void
    {
        $this->assertNull(OpportunityStageEnum::tryFrom($invalidValue));
    }

    public function testValuesAreUnique(): void
    {
        $values = array_map(fn (OpportunityStageEnum $case) => $case->value, OpportunityStageEnum::cases());
        $uniqueValues = array_unique($values);

        $this->assertCount(count($values), $uniqueValues, 'All enum values must be unique');
    }

    public function testLabelsAreUnique(): void
    {
        $labels = array_map(fn (OpportunityStageEnum $case) => $case->getLabel(), OpportunityStageEnum::cases());
        $uniqueLabels = array_unique($labels);

        $this->assertCount(count($labels), $uniqueLabels, 'All enum labels must be unique');
    }

    public function testBadgesAreValid(): void
    {
        $validBadges = ['success', 'warning', 'danger', 'info', 'primary', 'secondary', 'light', 'dark'];

        foreach (OpportunityStageEnum::cases() as $case) {
            $this->assertContains($case->getBadge(), $validBadges, "Badge '{$case->getBadge()}' is not valid");
        }
    }

    public function testToArray(): void
    {
        $result = OpportunityStageEnum::IDENTIFY_NEEDS->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'identify_needs', 'label' => '识别需求'], $result);

        $result = OpportunityStageEnum::SOLUTION_DESIGN->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'solution_design', 'label' => '方案制作'], $result);

        $result = OpportunityStageEnum::BUSINESS_NEGOTIATION->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'business_negotiation', 'label' => '商务谈判'], $result);

        $result = OpportunityStageEnum::CONTRACT_SIGNING->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'contract_signing', 'label' => '合同签署'], $result);

        $result = OpportunityStageEnum::CLOSED_WON->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'closed_won', 'label' => '已成交'], $result);

        $result = OpportunityStageEnum::CLOSED_LOST->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'closed_lost', 'label' => '已关闭'], $result);
    }

    public function testGenOptions(): void
    {
        $options = OpportunityStageEnum::genOptions();
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
            'label' => '识别需求',
            'text' => '识别需求',
            'value' => 'identify_needs',
            'name' => '识别需求',
        ], $options[0]);

        $this->assertEquals([
            'label' => '方案制作',
            'text' => '方案制作',
            'value' => 'solution_design',
            'name' => '方案制作',
        ], $options[1]);

        $this->assertEquals([
            'label' => '商务谈判',
            'text' => '商务谈判',
            'value' => 'business_negotiation',
            'name' => '商务谈判',
        ], $options[2]);

        $this->assertEquals([
            'label' => '合同签署',
            'text' => '合同签署',
            'value' => 'contract_signing',
            'name' => '合同签署',
        ], $options[3]);

        $this->assertEquals([
            'label' => '已成交',
            'text' => '已成交',
            'value' => 'closed_won',
            'name' => '已成交',
        ], $options[4]);

        $this->assertEquals([
            'label' => '已关闭',
            'text' => '已关闭',
            'value' => 'closed_lost',
            'name' => '已关闭',
        ], $options[5]);
    }
}
