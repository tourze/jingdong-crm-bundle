<?php

namespace JingdongCrmBundle\Tests\Enum;

use JingdongCrmBundle\Enum\ProductStatus;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use Tourze\PHPUnitEnum\AbstractEnumTestCase;

/**
 * @internal
 */
#[CoversClass(ProductStatus::class)]
final class ProductStatusTest extends AbstractEnumTestCase
{
    #[TestWith([ProductStatus::ON_SALE, 'on_sale', '在售', 'success'])]
    #[TestWith([ProductStatus::OFF_SHELF, 'off_shelf', '下架', 'secondary'])]
    #[TestWith([ProductStatus::OUT_OF_STOCK, 'out_of_stock', '缺货', 'warning'])]
    public function testEnumValueLabelAndBadge(ProductStatus $enum, string $expectedValue, string $expectedLabel, string $expectedBadge): void
    {
        $this->assertSame($expectedValue, $enum->value);
        $this->assertSame($expectedLabel, $enum->getLabel());
        $this->assertSame($expectedBadge, $enum->getBadge());
    }

    public function testAllCasesExist(): void
    {
        $cases = ProductStatus::cases();

        $this->assertCount(3, $cases);
        $this->assertContains(ProductStatus::ON_SALE, $cases);
        $this->assertContains(ProductStatus::OFF_SHELF, $cases);
        $this->assertContains(ProductStatus::OUT_OF_STOCK, $cases);
    }

    #[TestWith(['on_sale', ProductStatus::ON_SALE])]
    #[TestWith(['off_shelf', ProductStatus::OFF_SHELF])]
    #[TestWith(['out_of_stock', ProductStatus::OUT_OF_STOCK])]
    public function testFromReturnsCorrectEnum(string $value, ProductStatus $expectedEnum): void
    {
        $this->assertSame($expectedEnum, ProductStatus::from($value));
    }

    #[TestWith(['invalid_value'])]
    #[TestWith([''])]
    #[TestWith(['null'])]
    #[TestWith(['unknown'])]
    public function testFromThrowsValueErrorWithInvalidValue(string $invalidValue): void
    {
        $this->expectException(\ValueError::class);
        ProductStatus::from($invalidValue);
    }

    #[TestWith(['on_sale', ProductStatus::ON_SALE])]
    #[TestWith(['off_shelf', ProductStatus::OFF_SHELF])]
    #[TestWith(['out_of_stock', ProductStatus::OUT_OF_STOCK])]
    public function testTryFromReturnsEnumWithValidValue(string $value, ProductStatus $expectedEnum): void
    {
        $this->assertSame($expectedEnum, ProductStatus::tryFrom($value));
    }

    #[TestWith(['invalid_value'])]
    #[TestWith([''])]
    #[TestWith(['null'])]
    #[TestWith(['unknown'])]
    public function testTryFromReturnsNullWithInvalidValue(string $invalidValue): void
    {
        $this->assertNull(ProductStatus::tryFrom($invalidValue));
    }

    public function testValuesAreUnique(): void
    {
        $values = array_map(fn (ProductStatus $case) => $case->value, ProductStatus::cases());
        $uniqueValues = array_unique($values);

        $this->assertCount(count($values), $uniqueValues, 'All enum values must be unique');
    }

    public function testLabelsAreUnique(): void
    {
        $labels = array_map(fn (ProductStatus $case) => $case->getLabel(), ProductStatus::cases());
        $uniqueLabels = array_unique($labels);

        $this->assertCount(count($labels), $uniqueLabels, 'All enum labels must be unique');
    }

    public function testBadgesAreValid(): void
    {
        $validBadges = ['success', 'warning', 'danger', 'info', 'primary', 'secondary', 'light', 'dark'];

        foreach (ProductStatus::cases() as $case) {
            $this->assertContains($case->getBadge(), $validBadges, "Badge '{$case->getBadge()}' is not valid");
        }
    }

    public function testToArray(): void
    {
        $result = ProductStatus::ON_SALE->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'on_sale', 'label' => '在售'], $result);

        $result = ProductStatus::OFF_SHELF->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'off_shelf', 'label' => '下架'], $result);

        $result = ProductStatus::OUT_OF_STOCK->toArray();
        $this->assertIsArray($result);
        $this->assertEquals(['value' => 'out_of_stock', 'label' => '缺货'], $result);
    }

    public function testGenOptions(): void
    {
        $options = ProductStatus::genOptions();
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
            'label' => '在售',
            'text' => '在售',
            'value' => 'on_sale',
            'name' => '在售',
        ], $options[0]);

        $this->assertEquals([
            'label' => '下架',
            'text' => '下架',
            'value' => 'off_shelf',
            'name' => '下架',
        ], $options[1]);

        $this->assertEquals([
            'label' => '缺货',
            'text' => '缺货',
            'value' => 'out_of_stock',
            'name' => '缺货',
        ], $options[2]);
    }

    public function testLabel(): void
    {
        $this->assertSame('在售', ProductStatus::ON_SALE->label());
        $this->assertSame('下架', ProductStatus::OFF_SHELF->label());
        $this->assertSame('缺货', ProductStatus::OUT_OF_STOCK->label());
    }

    public function testIsOnSale(): void
    {
        $this->assertTrue(ProductStatus::ON_SALE->isOnSale());
        $this->assertFalse(ProductStatus::OFF_SHELF->isOnSale());
        $this->assertFalse(ProductStatus::OUT_OF_STOCK->isOnSale());
    }

    public function testIsOffShelf(): void
    {
        $this->assertFalse(ProductStatus::ON_SALE->isOffShelf());
        $this->assertTrue(ProductStatus::OFF_SHELF->isOffShelf());
        $this->assertFalse(ProductStatus::OUT_OF_STOCK->isOffShelf());
    }

    public function testIsOutOfStock(): void
    {
        $this->assertFalse(ProductStatus::ON_SALE->isOutOfStock());
        $this->assertFalse(ProductStatus::OFF_SHELF->isOutOfStock());
        $this->assertTrue(ProductStatus::OUT_OF_STOCK->isOutOfStock());
    }
}
