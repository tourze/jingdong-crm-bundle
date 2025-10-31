<?php

namespace JingdongCrmBundle\Tests\Enum;

use JingdongCrmBundle\Enum\LeadStatus;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(LeadStatus::class)]
final class LeadStatusTest extends AbstractEnumTestCase
{
    #[TestWith([LeadStatus::NEW, 'new', '新建', 'info'])]
    #[TestWith([LeadStatus::IN_PROGRESS, 'in_progress', '跟进中', 'warning'])]
    #[TestWith([LeadStatus::CONVERTED, 'converted', '已转化', 'success'])]
    #[TestWith([LeadStatus::CLOSED, 'closed', '已关闭', 'secondary'])]
    public function testEnumValueLabelAndBadge(LeadStatus $enum, string $expectedValue, string $expectedLabel, string $expectedBadge): void
    {
        $this->assertSame($expectedValue, $enum->value);
        $this->assertSame($expectedLabel, $enum->getLabel());
        $this->assertSame($expectedBadge, $enum->getBadge());
    }

    public function testAllCasesExist(): void
    {
        $cases = LeadStatus::cases();

        $this->assertCount(4, $cases);
        $this->assertContains(LeadStatus::NEW, $cases);
        $this->assertContains(LeadStatus::IN_PROGRESS, $cases);
        $this->assertContains(LeadStatus::CONVERTED, $cases);
        $this->assertContains(LeadStatus::CLOSED, $cases);
    }

    #[TestWith(['new', LeadStatus::NEW])]
    #[TestWith(['in_progress', LeadStatus::IN_PROGRESS])]
    #[TestWith(['converted', LeadStatus::CONVERTED])]
    #[TestWith(['closed', LeadStatus::CLOSED])]
    public function testFromReturnsCorrectEnum(string $value, LeadStatus $expectedEnum): void
    {
        $this->assertSame($expectedEnum, LeadStatus::from($value));
    }

    #[TestWith(['invalid_value'])]
    #[TestWith([''])]
    #[TestWith(['null'])]
    #[TestWith(['unknown'])]
    public function testFromThrowsValueErrorWithInvalidValue(string $invalidValue): void
    {
        $this->expectException(\ValueError::class);
        LeadStatus::from($invalidValue);
    }

    #[TestWith(['new', LeadStatus::NEW])]
    #[TestWith(['in_progress', LeadStatus::IN_PROGRESS])]
    #[TestWith(['converted', LeadStatus::CONVERTED])]
    #[TestWith(['closed', LeadStatus::CLOSED])]
    public function testTryFromReturnsEnumWithValidValue(string $value, LeadStatus $expectedEnum): void
    {
        $this->assertSame($expectedEnum, LeadStatus::tryFrom($value));
    }

    #[TestWith(['invalid_value'])]
    #[TestWith([''])]
    #[TestWith(['null'])]
    #[TestWith(['unknown'])]
    public function testTryFromReturnsNullWithInvalidValue(string $invalidValue): void
    {
        $this->assertNull(LeadStatus::tryFrom($invalidValue));
    }

    public function testValuesAreUnique(): void
    {
        $values = array_map(fn (LeadStatus $case) => $case->value, LeadStatus::cases());
        $uniqueValues = array_unique($values);

        $this->assertCount(count($values), $uniqueValues, 'All enum values must be unique');
    }

    public function testLabelsAreUnique(): void
    {
        $labels = array_map(fn (LeadStatus $case) => $case->getLabel(), LeadStatus::cases());
        $uniqueLabels = array_unique($labels);

        $this->assertCount(count($labels), $uniqueLabels, 'All enum labels must be unique');
    }

    public function testBadgesAreValid(): void
    {
        $validBadges = ['success', 'warning', 'danger', 'info', 'primary', 'secondary', 'light', 'dark'];

        foreach (LeadStatus::cases() as $case) {
            $this->assertContains($case->getBadge(), $validBadges, "Badge '{$case->getBadge()}' is not valid");
        }
    }

    public function testToArray(): void
    {
        $result = LeadStatus::NEW->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'new', 'label' => '新建'], $result);

        $result = LeadStatus::IN_PROGRESS->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'in_progress', 'label' => '跟进中'], $result);

        $result = LeadStatus::CONVERTED->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'converted', 'label' => '已转化'], $result);

        $result = LeadStatus::CLOSED->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'closed', 'label' => '已关闭'], $result);
    }

    public function testGenOptions(): void
    {
        $options = LeadStatus::genOptions();
        $this->assertIsArray($options);
        $this->assertCount(4, $options);

        foreach ($options as $item) {
            $this->assertArrayHasKey('value', $item);
            $this->assertArrayHasKey('label', $item);
            $this->assertArrayHasKey('text', $item);
            $this->assertArrayHasKey('name', $item);
        }

        // Test specific values
        $this->assertEquals([
            'label' => '新建',
            'text' => '新建',
            'value' => 'new',
            'name' => '新建',
        ], $options[0]);

        $this->assertEquals([
            'label' => '跟进中',
            'text' => '跟进中',
            'value' => 'in_progress',
            'name' => '跟进中',
        ], $options[1]);

        $this->assertEquals([
            'label' => '已转化',
            'text' => '已转化',
            'value' => 'converted',
            'name' => '已转化',
        ], $options[2]);

        $this->assertEquals([
            'label' => '已关闭',
            'text' => '已关闭',
            'value' => 'closed',
            'name' => '已关闭',
        ], $options[3]);
    }
}
