<?php

namespace JingdongCrmBundle\Tests\Enum;

use JingdongCrmBundle\Enum\LeadSource;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(LeadSource::class)]
final class LeadSourceTest extends AbstractEnumTestCase
{
    #[TestWith([LeadSource::WEBSITE, 'website', '网站', 'primary'])]
    #[TestWith([LeadSource::PHONE, 'phone', '电话', 'info'])]
    #[TestWith([LeadSource::ADVERTISEMENT, 'advertisement', '广告', 'warning'])]
    #[TestWith([LeadSource::REFERRAL, 'referral', '推荐', 'success'])]
    #[TestWith([LeadSource::OTHER, 'other', '其他', 'secondary'])]
    public function testEnumValueLabelAndBadge(LeadSource $enum, string $expectedValue, string $expectedLabel, string $expectedBadge): void
    {
        $this->assertSame($expectedValue, $enum->value);
        $this->assertSame($expectedLabel, $enum->getLabel());
        $this->assertSame($expectedBadge, $enum->getBadge());
    }

    public function testAllCasesExist(): void
    {
        $cases = LeadSource::cases();

        $this->assertCount(5, $cases);
        $this->assertContains(LeadSource::WEBSITE, $cases);
        $this->assertContains(LeadSource::PHONE, $cases);
        $this->assertContains(LeadSource::ADVERTISEMENT, $cases);
        $this->assertContains(LeadSource::REFERRAL, $cases);
        $this->assertContains(LeadSource::OTHER, $cases);
    }

    #[TestWith(['website', LeadSource::WEBSITE])]
    #[TestWith(['phone', LeadSource::PHONE])]
    #[TestWith(['advertisement', LeadSource::ADVERTISEMENT])]
    #[TestWith(['referral', LeadSource::REFERRAL])]
    #[TestWith(['other', LeadSource::OTHER])]
    public function testFromReturnsCorrectEnum(string $value, LeadSource $expectedEnum): void
    {
        $this->assertSame($expectedEnum, LeadSource::from($value));
    }

    #[TestWith(['invalid_value'])]
    #[TestWith([''])]
    #[TestWith(['null'])]
    #[TestWith(['unknown'])]
    public function testFromThrowsValueErrorWithInvalidValue(string $invalidValue): void
    {
        $this->expectException(\ValueError::class);
        LeadSource::from($invalidValue);
    }

    #[TestWith(['website', LeadSource::WEBSITE])]
    #[TestWith(['phone', LeadSource::PHONE])]
    #[TestWith(['advertisement', LeadSource::ADVERTISEMENT])]
    #[TestWith(['referral', LeadSource::REFERRAL])]
    #[TestWith(['other', LeadSource::OTHER])]
    public function testTryFromReturnsEnumWithValidValue(string $value, LeadSource $expectedEnum): void
    {
        $this->assertSame($expectedEnum, LeadSource::tryFrom($value));
    }

    #[TestWith(['invalid_value'])]
    #[TestWith([''])]
    #[TestWith(['null'])]
    #[TestWith(['unknown'])]
    public function testTryFromReturnsNullWithInvalidValue(string $invalidValue): void
    {
        $this->assertNull(LeadSource::tryFrom($invalidValue));
    }

    public function testValuesAreUnique(): void
    {
        $values = array_map(fn (LeadSource $case) => $case->value, LeadSource::cases());
        $uniqueValues = array_unique($values);

        $this->assertCount(count($values), $uniqueValues, 'All enum values must be unique');
    }

    public function testLabelsAreUnique(): void
    {
        $labels = array_map(fn (LeadSource $case) => $case->getLabel(), LeadSource::cases());
        $uniqueLabels = array_unique($labels);

        $this->assertCount(count($labels), $uniqueLabels, 'All enum labels must be unique');
    }

    public function testBadgesAreValid(): void
    {
        $validBadges = ['success', 'warning', 'danger', 'info', 'primary', 'secondary', 'light', 'dark'];

        foreach (LeadSource::cases() as $case) {
            $this->assertContains($case->getBadge(), $validBadges, "Badge '{$case->getBadge()}' is not valid");
        }
    }

    public function testToArray(): void
    {
        $result = LeadSource::WEBSITE->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'website', 'label' => '网站'], $result);

        $result = LeadSource::PHONE->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'phone', 'label' => '电话'], $result);

        $result = LeadSource::ADVERTISEMENT->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'advertisement', 'label' => '广告'], $result);

        $result = LeadSource::REFERRAL->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'referral', 'label' => '推荐'], $result);

        $result = LeadSource::OTHER->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'other', 'label' => '其他'], $result);
    }

    public function testGenOptions(): void
    {
        $options = LeadSource::genOptions();
        $this->assertIsArray($options);
        $this->assertCount(5, $options);

        foreach ($options as $item) {
            $this->assertArrayHasKey('value', $item);
            $this->assertArrayHasKey('label', $item);
            $this->assertArrayHasKey('text', $item);
            $this->assertArrayHasKey('name', $item);
        }

        // Test specific values
        $this->assertEquals([
            'label' => '网站',
            'text' => '网站',
            'value' => 'website',
            'name' => '网站',
        ], $options[0]);

        $this->assertEquals([
            'label' => '电话',
            'text' => '电话',
            'value' => 'phone',
            'name' => '电话',
        ], $options[1]);

        $this->assertEquals([
            'label' => '广告',
            'text' => '广告',
            'value' => 'advertisement',
            'name' => '广告',
        ], $options[2]);

        $this->assertEquals([
            'label' => '推荐',
            'text' => '推荐',
            'value' => 'referral',
            'name' => '推荐',
        ], $options[3]);

        $this->assertEquals([
            'label' => '其他',
            'text' => '其他',
            'value' => 'other',
            'name' => '其他',
        ], $options[4]);
    }
}
