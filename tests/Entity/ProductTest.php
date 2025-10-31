<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Tests\Entity;

use JingdongCrmBundle\Entity\Product;
use JingdongCrmBundle\Enum\ProductStatus;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Product::class)]
final class ProductTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Product();
    }

    /**
     * @return array<array{string, mixed}>
     */
    public static function propertiesProvider(): array
    {
        return [
            ['productCode', 'PROD001'],
            ['name', 'Test Product'],
            ['category', 'Electronics'],
            ['description', 'Test description'],
            ['price', '99.99'],
            ['unit', 'pcs'],
            ['status', ProductStatus::OFF_SHELF],
            ['jdProductId', 'JD123456'],
        ];
    }

    public function testDefaultValues(): void
    {
        $product = new Product();

        self::assertSame(ProductStatus::ON_SALE, $product->getStatus());
        self::assertSame('', $product->getProductCode());
        self::assertSame('', $product->getName());
        self::assertSame('0.00', $product->getPrice());
        self::assertSame(0, $product->getId());
    }

    public function testGetSetProductCode(): void
    {
        $product = new Product();
        $productCode = 'PROD001';

        $product->setProductCode($productCode);
        self::assertSame($productCode, $product->getProductCode());
    }

    public function testGetSetName(): void
    {
        $product = new Product();
        $name = 'Test Product';

        $product->setName($name);
        self::assertSame($name, $product->getName());
    }

    public function testGetSetCategory(): void
    {
        $product = new Product();
        $category = 'Electronics';

        $product->setCategory($category);
        self::assertSame($category, $product->getCategory());

        $product->setCategory(null);
        self::assertNull($product->getCategory());
    }

    public function testGetSetDescription(): void
    {
        $product = new Product();
        $description = 'Test description';

        $product->setDescription($description);
        self::assertSame($description, $product->getDescription());

        $product->setDescription(null);
        self::assertNull($product->getDescription());
    }

    public function testGetSetPrice(): void
    {
        $product = new Product();

        self::assertSame('0.00', $product->getPrice());

        $product->setPrice('99.99');
        self::assertSame('99.99', $product->getPrice());
    }

    public function testGetSetUnit(): void
    {
        $product = new Product();
        $unit = 'pcs';

        $product->setUnit($unit);
        self::assertSame($unit, $product->getUnit());

        $product->setUnit(null);
        self::assertNull($product->getUnit());
    }

    public function testGetSetStatus(): void
    {
        $product = new Product();

        self::assertSame(ProductStatus::ON_SALE, $product->getStatus());

        $product->setStatus(ProductStatus::OFF_SHELF);
        self::assertSame(ProductStatus::OFF_SHELF, $product->getStatus());
    }

    public function testGetSetJdProductId(): void
    {
        $product = new Product();
        $jdProductId = 'JD123456';

        $product->setJdProductId($jdProductId);
        self::assertSame($jdProductId, $product->getJdProductId());

        $product->setJdProductId(null);
        self::assertNull($product->getJdProductId());
    }

    public function testGetId(): void
    {
        $product = new Product();

        self::assertSame(0, $product->getId());
    }

    public function testIsOnSale(): void
    {
        $product = new Product();

        // Default status is ON_SALE
        self::assertTrue($product->isOnSale());

        $product->setStatus(ProductStatus::OFF_SHELF);
        self::assertFalse($product->isOnSale());
    }

    public function testIsOffShelf(): void
    {
        $product = new Product();

        // Default status is ON_SALE
        self::assertFalse($product->isOffShelf());

        $product->setStatus(ProductStatus::OFF_SHELF);
        self::assertTrue($product->isOffShelf());
    }

    public function testIsOutOfStock(): void
    {
        $product = new Product();

        // Default status is ON_SALE
        self::assertFalse($product->isOutOfStock());

        $product->setStatus(ProductStatus::OUT_OF_STOCK);
        self::assertTrue($product->isOutOfStock());
    }

    public function testToStringWithName(): void
    {
        $product = new Product();
        $product->setName('Test Product');
        $product->setProductCode('PROD001');

        self::assertSame('Test Product', (string) $product);
    }

    public function testToStringWithoutName(): void
    {
        $product = new Product();
        $product->setProductCode('PROD001');

        self::assertSame('PROD001', (string) $product);
    }

    public function testToStringWithEmptyNameAndCode(): void
    {
        $product = new Product();

        // Both name and productCode are empty strings by default
        self::assertSame('', (string) $product);
    }
}
