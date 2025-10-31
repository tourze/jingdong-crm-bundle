<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Tests\Repository;

use JingdongCrmBundle\Entity\Product;
use JingdongCrmBundle\Enum\ProductStatus;
use JingdongCrmBundle\Repository\ProductRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * ProductRepository测试类
 * @internal
 */
#[CoversClass(ProductRepository::class)]
#[RunTestsInSeparateProcesses]
final class ProductRepositoryTest extends AbstractRepositoryTestCase
{
    protected function createNewEntity(): object
    {
        $product = new Product();
        $product->setProductCode('TEST-' . uniqid());
        $product->setName('测试产品');
        $product->setPrice('99.99');

        return $product;
    }

    protected function getRepository(): ProductRepository
    {
        return self::getService(ProductRepository::class);
    }

    protected function onSetUp(): void
    {
        // 测试设置完成后的操作
    }

    public function testFindByProductCode(): void
    {
        $repository = $this->getRepository();

        // 创建测试产品
        $product = new Product();
        $productCode = 'TEST-REPO-' . uniqid();
        $product->setProductCode($productCode);
        $product->setName('仓储测试产品');
        $product->setPrice('99.99');

        $repository->save($product, true);

        // 测试正常查找
        $foundProduct = $repository->findByProductCode($productCode);
        $this->assertNotNull($foundProduct);
        $this->assertEquals($productCode, $foundProduct->getProductCode());
        $this->assertEquals('仓储测试产品', $foundProduct->getName());

        // 测试查找不存在的产品编码
        $notFoundProduct = $repository->findByProductCode('NON-EXISTENT-CODE');
        $this->assertNull($notFoundProduct);
    }

    public function testFindByCategory(): void
    {
        $repository = $this->getRepository();

        // 创建不同分类的测试产品
        $product1 = new Product();
        $product1->setProductCode('CAT-TEST-1-' . uniqid());
        $product1->setName('电子产品A');
        $product1->setCategory('电子产品');
        $product1->setPrice('199.99');

        $product2 = new Product();
        $product2->setProductCode('CAT-TEST-2-' . uniqid());
        $product2->setName('电子产品B');
        $product2->setCategory('电子产品');
        $product2->setPrice('299.99');

        $product3 = new Product();
        $product3->setProductCode('CAT-TEST-3-' . uniqid());
        $product3->setName('家居产品A');
        $product3->setCategory('家居用品');
        $product3->setPrice('89.99');

        $repository->save($product1, true);
        $repository->save($product2, true);
        $repository->save($product3, true);

        // 测试按分类查找
        $electronicProducts = $repository->findByCategory('电子产品');
        $this->assertCount(2, $electronicProducts);
        $this->assertEquals('电子产品A', $electronicProducts[0]->getName());
        $this->assertEquals('电子产品B', $electronicProducts[1]->getName());

        $homeProducts = $repository->findByCategory('家居用品');
        $this->assertCount(1, $homeProducts);
        $this->assertEquals('家居产品A', $homeProducts[0]->getName());

        // 测试查找不存在的分类
        $nonExistentCategory = $repository->findByCategory('不存在的分类');
        $this->assertEmpty($nonExistentCategory);
    }

    public function testFindOnSaleProducts(): void
    {
        $repository = $this->getRepository();

        // 创建不同状态的测试产品
        $product1 = new Product();
        $product1->setProductCode('SALE-TEST-1-' . uniqid());
        $product1->setName('在售产品A');
        $product1->setPrice('99.99');
        $product1->setStatus(ProductStatus::ON_SALE);

        $product2 = new Product();
        $product2->setProductCode('SALE-TEST-2-' . uniqid());
        $product2->setName('在售产品B');
        $product2->setPrice('199.99');
        $product2->setStatus(ProductStatus::ON_SALE);

        $product3 = new Product();
        $product3->setProductCode('SALE-TEST-3-' . uniqid());
        $product3->setName('下架产品');
        $product3->setPrice('149.99');
        $product3->setStatus(ProductStatus::OFF_SHELF);

        $product4 = new Product();
        $product4->setProductCode('SALE-TEST-4-' . uniqid());
        $product4->setName('缺货产品');
        $product4->setPrice('79.99');
        $product4->setStatus(ProductStatus::OUT_OF_STOCK);

        $repository->save($product1, true);
        $repository->save($product2, true);
        $repository->save($product3, true);
        $repository->save($product4, true);

        // 测试查找在售产品（过滤只包含当前测试创建的数据）
        $allOnSaleProducts = $repository->findOnSaleProducts();
        $onSaleProducts = array_filter($allOnSaleProducts, function (Product $p) {
            return str_starts_with($p->getProductCode(), 'SALE-TEST-');
        });
        $onSaleProducts = array_values($onSaleProducts); // 重新索引
        $this->assertCount(2, $onSaleProducts);

        // 验证产品名称按字母顺序排序
        $this->assertEquals('在售产品A', $onSaleProducts[0]->getName());
        $this->assertEquals('在售产品B', $onSaleProducts[1]->getName());

        // 验证所有产品都是在售状态
        foreach ($onSaleProducts as $product) {
            $this->assertEquals(ProductStatus::ON_SALE, $product->getStatus());
            $this->assertTrue($product->isOnSale());
        }
    }

    public function testSaveAndRemove(): void
    {
        $repository = $this->getRepository();

        // 测试save方法
        $product = new Product();
        $productCode = 'SAVE-TEST-' . uniqid();
        $product->setProductCode($productCode);
        $product->setName('保存测试产品');
        $product->setPrice('88.88');

        // 测试不flush的save
        $repository->save($product, false);
        $foundBeforeFlush = $repository->findByProductCode($productCode);
        $this->assertNull($foundBeforeFlush); // 还未flush，不应该找到

        // 手动flush
        $entityManager = self::getEntityManager();
        $entityManager->flush();
        $foundAfterFlush = $repository->findByProductCode($productCode);
        $this->assertNotNull($foundAfterFlush);
        $this->assertEquals($productCode, $foundAfterFlush->getProductCode());

        // 测试带flush的save
        $product2 = new Product();
        $productCode2 = 'SAVE-TEST-2-' . uniqid();
        $product2->setProductCode($productCode2);
        $product2->setName('保存测试产品2');
        $product2->setPrice('77.77');

        $repository->save($product2, true);
        $foundImmediately = $repository->findByProductCode($productCode2);
        $this->assertNotNull($foundImmediately);
        $this->assertEquals($productCode2, $foundImmediately->getProductCode());

        // 测试remove方法
        $repository->remove($foundImmediately, true);
        $removedProduct = $repository->findByProductCode($productCode2);
        $this->assertNull($removedProduct);
    }

    public function testSaveWithoutFlush(): void
    {
        $repository = $this->getRepository();

        $product = new Product();
        $productCode = 'NO-FLUSH-TEST-' . uniqid();
        $product->setProductCode($productCode);
        $product->setName('无刷新测试产品');
        $product->setPrice('55.55');

        // 不使用flush的save
        $repository->save($product, false);

        // 在同一事务中应该能找到
        $entityManager = self::getEntityManager();
        $persistedProduct = $entityManager->find(Product::class, $product->getId());
        $this->assertNull($persistedProduct); // ID还没有生成

        // flush后应该能找到
        $entityManager->flush();
        $foundProduct = $repository->findByProductCode($productCode);
        $this->assertNotNull($foundProduct);
        $this->assertEquals('无刷新测试产品', $foundProduct->getName());
    }

    public function testRemoveWithoutFlushCustom(): void
    {
        $repository = $this->getRepository();

        // 先创建一个产品
        $product = new Product();
        $productCode = 'REMOVE-TEST-' . uniqid();
        $product->setProductCode($productCode);
        $product->setName('删除测试产品');
        $product->setPrice('33.33');

        $repository->save($product, true);
        $this->assertNotNull($repository->findByProductCode($productCode));

        // 不使用flush的remove
        $repository->remove($product, false);

        // 在同一事务中仍然可以找到
        $stillExists = $repository->findByProductCode($productCode);
        $this->assertNotNull($stillExists);

        // flush后应该被删除
        $entityManager = self::getEntityManager();
        $entityManager->flush();
        $removedProduct = $repository->findByProductCode($productCode);
        $this->assertNull($removedProduct);
    }
}
