<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Tests\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use JingdongCrmBundle\Controller\Admin\ProductCrudController;
use JingdongCrmBundle\Entity\Product;
use JingdongCrmBundle\Enum\ProductStatus;
use JingdongCrmBundle\Repository\ProductRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminControllerTestCase;

/**
 * ProductCrudController测试类
 * @internal
 */
#[CoversClass(ProductCrudController::class)]
#[RunTestsInSeparateProcesses]
final class ProductCrudControllerTest extends AbstractEasyAdminControllerTestCase
{
    /**
     * @return AbstractCrudController<Product>
     */
    protected function getControllerService(): AbstractCrudController
    {
        return self::getService(ProductCrudController::class);
    }

    public static function provideIndexPageHeaders(): iterable
    {
        yield 'ID' => ['ID'];
        yield '产品编码' => ['产品编码'];
        yield '产品名称' => ['产品名称'];
        yield '产品分类' => ['产品分类'];
        yield '产品价格' => ['产品价格'];
        yield '产品状态' => ['产品状态'];
    }

    public static function provideNewPageFields(): iterable
    {
        yield 'productCode' => ['productCode'];
        yield 'name' => ['name'];
        yield 'category' => ['category'];
        yield 'unit' => ['unit'];
        yield 'price' => ['price'];
        yield 'status' => ['status'];
        yield 'description' => ['description'];
        yield 'jdProductId' => ['jdProductId'];
    }

    public static function provideEditPageFields(): iterable
    {
        return self::provideNewPageFields();
    }

    protected function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function testIndexPage(): void
    {
        $client = self::createClientWithDatabase();
        $this->loginAsAdmin($client);
        $crawler = $client->request('GET', '/admin');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Navigate to Product CRUD
        $link = $crawler->filter('a[href*="ProductCrudController"]')->first();
        if ($link->count() > 0) {
            $client->click($link->link());
            $this->assertEquals(200, $client->getResponse()->getStatusCode());
        }
    }

    public function testCreateProduct(): void
    {
        $client = self::createClientWithDatabase();
        $this->loginAsAdmin($client);
        $client->request('GET', '/admin');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Create test product
        $product = new Product();
        $product->setProductCode('TEST-' . uniqid());
        $product->setName('测试产品-控制器');
        $product->setCategory('测试分类');
        $product->setPrice('99.99');
        $product->setUnit('件');
        $product->setStatus(ProductStatus::ON_SALE);
        $product->setDescription('这是一个测试产品的描述信息');
        $product->setJdProductId('JD-' . uniqid());

        $productRepository = self::getService(ProductRepository::class);
        self::assertInstanceOf(ProductRepository::class, $productRepository);
        $productRepository->save($product, true);

        // Verify product was created
        $savedProduct = $productRepository->findOneBy([
            'productCode' => $product->getProductCode(),
        ]);
        $this->assertNotNull($savedProduct);
        $this->assertEquals('测试产品-控制器', $savedProduct->getName());
        $this->assertEquals('99.99', $savedProduct->getPrice());
        $this->assertEquals(ProductStatus::ON_SALE, $savedProduct->getStatus());
    }

    public function testProductDataPersistence(): void
    {
        // Create client to initialize database
        $client = self::createClientWithDatabase();

        // Create test products with different statuses
        $product1 = new Product();
        $product1->setProductCode('SEARCH-TEST-1-' . uniqid());
        $product1->setName('搜索测试产品一');
        $product1->setCategory('电子产品');
        $product1->setPrice('199.99');
        $product1->setUnit('台');
        $product1->setStatus(ProductStatus::ON_SALE);
        $product1->setDescription('第一个搜索测试产品的详细描述');
        $product1->setJdProductId('JD-TEST-1-' . uniqid());

        $productRepository = self::getService(ProductRepository::class);
        self::assertInstanceOf(ProductRepository::class, $productRepository);
        $productRepository->save($product1, true);

        $product2 = new Product();
        $product2->setProductCode('SEARCH-TEST-2-' . uniqid());
        $product2->setName('搜索测试产品二');
        $product2->setCategory('家居用品');
        $product2->setPrice('299.50');
        $product2->setUnit('套');
        $product2->setStatus(ProductStatus::OFF_SHELF);
        $product2->setDescription('第二个搜索测试产品的详细描述');

        $productRepository->save($product2, true);

        $product3 = new Product();
        $product3->setProductCode('SEARCH-TEST-3-' . uniqid());
        $product3->setName('搜索测试产品三');
        $product3->setCategory('服装鞋帽');
        $product3->setPrice('89.00');
        $product3->setUnit('件');
        $product3->setStatus(ProductStatus::OUT_OF_STOCK);
        $product3->setDescription('第三个搜索测试产品的详细描述');
        $product3->setJdProductId('JD-TEST-3-' . uniqid());

        $productRepository->save($product3, true);

        // Verify products are saved correctly
        $savedProduct1 = $productRepository->findOneBy(['productCode' => $product1->getProductCode()]);
        $this->assertNotNull($savedProduct1);
        $this->assertEquals('搜索测试产品一', $savedProduct1->getName());
        $this->assertEquals('电子产品', $savedProduct1->getCategory());
        $this->assertEquals('199.99', $savedProduct1->getPrice());
        $this->assertEquals(ProductStatus::ON_SALE, $savedProduct1->getStatus());
        $this->assertTrue($savedProduct1->isOnSale());
        $this->assertFalse($savedProduct1->isOffShelf());
        $this->assertFalse($savedProduct1->isOutOfStock());

        $savedProduct2 = $productRepository->findOneBy(['productCode' => $product2->getProductCode()]);
        $this->assertNotNull($savedProduct2);
        $this->assertEquals('搜索测试产品二', $savedProduct2->getName());
        $this->assertEquals('家居用品', $savedProduct2->getCategory());
        $this->assertEquals('299.50', $savedProduct2->getPrice());
        $this->assertEquals(ProductStatus::OFF_SHELF, $savedProduct2->getStatus());
        $this->assertFalse($savedProduct2->isOnSale());
        $this->assertTrue($savedProduct2->isOffShelf());
        $this->assertFalse($savedProduct2->isOutOfStock());

        $savedProduct3 = $productRepository->findOneBy(['productCode' => $product3->getProductCode()]);
        $this->assertNotNull($savedProduct3);
        $this->assertEquals('搜索测试产品三', $savedProduct3->getName());
        $this->assertEquals('服装鞋帽', $savedProduct3->getCategory());
        $this->assertEquals('89.00', $savedProduct3->getPrice());
        $this->assertEquals(ProductStatus::OUT_OF_STOCK, $savedProduct3->getStatus());
        $this->assertFalse($savedProduct3->isOnSale());
        $this->assertFalse($savedProduct3->isOffShelf());
        $this->assertTrue($savedProduct3->isOutOfStock());
    }

    public function testProductStatusBehavior(): void
    {
        $client = self::createClientWithDatabase();

        $product = new Product();
        $product->setProductCode('STATUS-TEST-' . uniqid());
        $product->setName('状态测试产品');
        $product->setPrice('100.00');
        $product->setStatus(ProductStatus::ON_SALE);

        $productRepository = self::getService(ProductRepository::class);
        self::assertInstanceOf(ProductRepository::class, $productRepository);
        $productRepository->save($product, true);

        $savedProduct = $productRepository->findOneBy(['productCode' => $product->getProductCode()]);
        $this->assertNotNull($savedProduct);

        // Test initial status
        $this->assertEquals(ProductStatus::ON_SALE, $savedProduct->getStatus());
        $this->assertTrue($savedProduct->isOnSale());

        // Test status change to OFF_SHELF
        $savedProduct->setStatus(ProductStatus::OFF_SHELF);
        $productRepository->save($savedProduct, true);

        $updatedProduct = $productRepository->findOneBy(['productCode' => $product->getProductCode()]);
        $this->assertNotNull($updatedProduct);
        $this->assertEquals(ProductStatus::OFF_SHELF, $updatedProduct->getStatus());
        $this->assertTrue($updatedProduct->isOffShelf());
        $this->assertFalse($updatedProduct->isOnSale());

        // Test status change to OUT_OF_STOCK
        $updatedProduct->setStatus(ProductStatus::OUT_OF_STOCK);
        $productRepository->save($updatedProduct, true);

        $finalProduct = $productRepository->findOneBy(['productCode' => $product->getProductCode()]);
        $this->assertNotNull($finalProduct);
        $this->assertEquals(ProductStatus::OUT_OF_STOCK, $finalProduct->getStatus());
        $this->assertTrue($finalProduct->isOutOfStock());
        $this->assertFalse($finalProduct->isOnSale());
        $this->assertFalse($finalProduct->isOffShelf());
    }

    public function testProductStringRepresentation(): void
    {
        $client = self::createClientWithDatabase();

        // Test with name
        $productWithName = new Product();
        $productWithName->setProductCode('STRING-TEST-1-' . uniqid());
        $productWithName->setName('产品名称测试');
        $productWithName->setPrice('50.00');

        $this->assertEquals('产品名称测试', (string) $productWithName);

        // Test without name (should return productCode)
        $productWithoutName = new Product();
        $productCode = 'STRING-TEST-2-' . uniqid();
        $productWithoutName->setProductCode($productCode);
        $productWithoutName->setPrice('60.00');

        $this->assertEquals($productCode, (string) $productWithoutName);
    }

    /**
     * 重写父类方法，验证Product实体的实际必填字段
     */
}
