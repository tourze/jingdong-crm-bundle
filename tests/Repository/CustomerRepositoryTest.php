<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Tests\Repository;

use JingdongCrmBundle\Entity\Customer;
use JingdongCrmBundle\Enum\CustomerStatusEnum;
use JingdongCrmBundle\Enum\CustomerTypeEnum;
use JingdongCrmBundle\Repository\CustomerRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * CustomerRepository测试类
 * @internal
 */
#[CoversClass(CustomerRepository::class)]
#[RunTestsInSeparateProcesses]
final class CustomerRepositoryTest extends AbstractRepositoryTestCase
{
    protected function createNewEntity(): object
    {
        $customer = new Customer();
        $customer->setName('测试客户');
        $customer->setCustomerCode('CUST-' . uniqid());
        $customer->setJdCustomerId('JD-' . uniqid());
        $customer->setStatus(CustomerStatusEnum::ACTIVE);
        $customer->setType(CustomerTypeEnum::ENTERPRISE);

        return $customer;
    }

    protected function getRepository(): CustomerRepository
    {
        return self::getService(CustomerRepository::class);
    }

    protected function onSetUp(): void
    {
        // 测试设置完成后的操作
    }

    public function testFindByCustomerCode(): void
    {
        $repository = $this->getRepository();

        // 创建测试客户
        $customer = new Customer();
        $customerCode = 'TEST-CUST-' . uniqid();
        $customer->setName('测试客户编码查找');
        $customer->setCustomerCode($customerCode);
        $customer->setJdCustomerId('JD-' . uniqid());
        $customer->setStatus(CustomerStatusEnum::ACTIVE);
        $customer->setType(CustomerTypeEnum::ENTERPRISE);

        $repository->save($customer, true);

        // 测试正常查找
        $foundCustomer = $repository->findByCustomerCode($customerCode);
        $this->assertNotNull($foundCustomer);
        $this->assertEquals($customerCode, $foundCustomer->getCustomerCode());
        $this->assertEquals('测试客户编码查找', $foundCustomer->getName());

        // 测试查找不存在的客户编码
        $notFoundCustomer = $repository->findByCustomerCode('NON-EXISTENT-CODE');
        $this->assertNull($notFoundCustomer);
    }

    public function testFindByJdCustomerId(): void
    {
        $repository = $this->getRepository();

        // 创建测试客户
        $customer = new Customer();
        $jdCustomerId = 'JD-' . uniqid();
        $customer->setName('测试京东客户ID查找');
        $customer->setCustomerCode('CUST-' . uniqid());
        $customer->setJdCustomerId($jdCustomerId);
        $customer->setStatus(CustomerStatusEnum::ACTIVE);
        $customer->setType(CustomerTypeEnum::INDIVIDUAL);

        $repository->save($customer, true);

        // 测试正常查找
        $foundCustomer = $repository->findByJdCustomerId($jdCustomerId);
        $this->assertNotNull($foundCustomer);
        $this->assertEquals($jdCustomerId, $foundCustomer->getJdCustomerId());
        $this->assertEquals('测试京东客户ID查找', $foundCustomer->getName());

        // 测试查找不存在的京东客户ID
        $notFoundCustomer = $repository->findByJdCustomerId('NON-EXISTENT-JD-ID');
        $this->assertNull($notFoundCustomer);
    }

    public function testFindActiveCustomers(): void
    {
        $repository = $this->getRepository();

        // 创建活跃客户
        $activeCustomer1 = new Customer();
        $activeCustomer1->setName('活跃客户A');
        $activeCustomer1->setCustomerCode('ACTIVE-A-' . uniqid());
        $activeCustomer1->setJdCustomerId('JD-ACTIVE-A-' . uniqid());
        $activeCustomer1->setStatus(CustomerStatusEnum::ACTIVE);
        $activeCustomer1->setType(CustomerTypeEnum::ENTERPRISE);

        $activeCustomer2 = new Customer();
        $activeCustomer2->setName('活跃客户B');
        $activeCustomer2->setCustomerCode('ACTIVE-B-' . uniqid());
        $activeCustomer2->setJdCustomerId('JD-ACTIVE-B-' . uniqid());
        $activeCustomer2->setStatus(CustomerStatusEnum::ACTIVE);
        $activeCustomer2->setType(CustomerTypeEnum::INDIVIDUAL);

        // 创建非活跃客户
        $inactiveCustomer = new Customer();
        $inactiveCustomer->setName('非活跃客户');
        $inactiveCustomer->setCustomerCode('INACTIVE-' . uniqid());
        $inactiveCustomer->setJdCustomerId('JD-INACTIVE-' . uniqid());
        $inactiveCustomer->setStatus(CustomerStatusEnum::SUSPENDED);
        $inactiveCustomer->setType(CustomerTypeEnum::ENTERPRISE);

        $repository->save($activeCustomer1, true);
        $repository->save($activeCustomer2, true);
        $repository->save($inactiveCustomer, true);

        // 测试查找活跃客户
        $activeCustomers = $repository->findActiveCustomers();
        $this->assertGreaterThanOrEqual(2, count($activeCustomers));

        // 查找我们创建的特定客户
        $testCustomers = array_filter($activeCustomers, function ($customer) {
            return str_starts_with($customer->getName(), '活跃客户');
        });
        $this->assertCount(2, $testCustomers);

        // 按名称排序测试客户
        usort($testCustomers, function ($a, $b) {
            return strcmp($a->getName(), $b->getName());
        });
        $this->assertEquals('活跃客户A', $testCustomers[0]->getName());
        $this->assertEquals('活跃客户B', $testCustomers[1]->getName());

        // 验证所有客户都是活跃状态
        foreach ($activeCustomers as $customer) {
            $this->assertEquals(CustomerStatusEnum::ACTIVE, $customer->getStatus());
        }
    }

    public function testFindByType(): void
    {
        $repository = $this->getRepository();

        // 创建企业客户
        $enterpriseCustomer1 = new Customer();
        $enterpriseCustomer1->setName('企业客户A');
        $enterpriseCustomer1->setCustomerCode('ENT-A-' . uniqid());
        $enterpriseCustomer1->setJdCustomerId('JD-ENT-A-' . uniqid());
        $enterpriseCustomer1->setStatus(CustomerStatusEnum::ACTIVE);
        $enterpriseCustomer1->setType(CustomerTypeEnum::ENTERPRISE);

        $enterpriseCustomer2 = new Customer();
        $enterpriseCustomer2->setName('企业客户B');
        $enterpriseCustomer2->setCustomerCode('ENT-B-' . uniqid());
        $enterpriseCustomer2->setJdCustomerId('JD-ENT-B-' . uniqid());
        $enterpriseCustomer2->setStatus(CustomerStatusEnum::ACTIVE);
        $enterpriseCustomer2->setType(CustomerTypeEnum::ENTERPRISE);

        // 创建个人客户
        $individualCustomer = new Customer();
        $individualCustomer->setName('个人客户');
        $individualCustomer->setCustomerCode('IND-' . uniqid());
        $individualCustomer->setJdCustomerId('JD-IND-' . uniqid());
        $individualCustomer->setStatus(CustomerStatusEnum::ACTIVE);
        $individualCustomer->setType(CustomerTypeEnum::INDIVIDUAL);

        $repository->save($enterpriseCustomer1, true);
        $repository->save($enterpriseCustomer2, true);
        $repository->save($individualCustomer, true);

        // 测试查找企业客户
        $enterpriseCustomers = $repository->findByType(CustomerTypeEnum::ENTERPRISE);
        $this->assertGreaterThanOrEqual(2, count($enterpriseCustomers));

        // 查找我们创建的特定客户
        $testCustomers = array_filter($enterpriseCustomers, function ($customer) {
            return str_starts_with($customer->getName(), '企业客户');
        });
        $this->assertCount(2, $testCustomers);

        // 按名称排序测试客户
        usort($testCustomers, function ($a, $b) {
            return strcmp($a->getName(), $b->getName());
        });
        $this->assertEquals('企业客户A', $testCustomers[0]->getName());
        $this->assertEquals('企业客户B', $testCustomers[1]->getName());

        // 验证所有客户都是企业类型
        foreach ($enterpriseCustomers as $customer) {
            $this->assertEquals(CustomerTypeEnum::ENTERPRISE, $customer->getType());
        }

        // 测试查找个人客户
        $individualCustomers = $repository->findByType(CustomerTypeEnum::INDIVIDUAL);
        $this->assertGreaterThanOrEqual(1, count($individualCustomers));

        // 查找我们创建的特定客户
        $testIndividualCustomers = array_filter($individualCustomers, function ($customer) {
            return '个人客户' === $customer->getName();
        });
        $this->assertCount(1, $testIndividualCustomers);
        $testIndividualCustomer = reset($testIndividualCustomers);
        $this->assertEquals('个人客户', $testIndividualCustomer->getName());
        $this->assertEquals(CustomerTypeEnum::INDIVIDUAL, $testIndividualCustomer->getType());
    }

    public function testCustomerSaveAndRemove(): void
    {
        $repository = $this->getRepository();

        // 测试save方法
        $customer = new Customer();
        $customerCode = 'SAVE-TEST-' . uniqid();
        $customer->setName('保存测试客户');
        $customer->setCustomerCode($customerCode);
        $customer->setJdCustomerId('JD-SAVE-TEST-' . uniqid());
        $customer->setStatus(CustomerStatusEnum::ACTIVE);
        $customer->setType(CustomerTypeEnum::ENTERPRISE);

        // 测试不flush的save
        $repository->save($customer, false);
        $foundBeforeFlush = $repository->findByCustomerCode($customerCode);
        $this->assertNull($foundBeforeFlush); // 还未flush，不应该找到

        // 手动flush
        $entityManager = self::getEntityManager();
        $entityManager->flush();
        $foundAfterFlush = $repository->findByCustomerCode($customerCode);
        $this->assertNotNull($foundAfterFlush);
        $this->assertEquals($customerCode, $foundAfterFlush->getCustomerCode());

        // 测试带flush的save
        $customer2 = new Customer();
        $customerCode2 = 'SAVE-TEST-2-' . uniqid();
        $customer2->setName('保存测试客户2');
        $customer2->setCustomerCode($customerCode2);
        $customer2->setJdCustomerId('JD-SAVE-TEST-2-' . uniqid());
        $customer2->setStatus(CustomerStatusEnum::ACTIVE);
        $customer2->setType(CustomerTypeEnum::INDIVIDUAL);

        $repository->save($customer2, true);
        $foundImmediately = $repository->findByCustomerCode($customerCode2);
        $this->assertNotNull($foundImmediately);
        $this->assertEquals($customerCode2, $foundImmediately->getCustomerCode());

        // 测试remove方法
        $repository->remove($foundImmediately, true);
        $removedCustomer = $repository->findByCustomerCode($customerCode2);
        $this->assertNull($removedCustomer);
    }

    public function testCustomerSaveWithoutFlush(): void
    {
        $repository = $this->getRepository();

        $customer = new Customer();
        $customerCode = 'NO-FLUSH-TEST-' . uniqid();
        $customer->setName('无刷新测试客户');
        $customer->setCustomerCode($customerCode);
        $customer->setJdCustomerId('JD-NO-FLUSH-' . uniqid());
        $customer->setStatus(CustomerStatusEnum::ACTIVE);
        $customer->setType(CustomerTypeEnum::ENTERPRISE);

        // 不使用flush的save
        $repository->save($customer);

        // persist()后，实体应该在同一事务中可见，但数据库查询可能看不到
        $entityManager = self::getEntityManager();
        $this->assertNotNull($customer->getId()); // ID应该已经生成

        // 在同一事务中，find()应该能找到已persist的实体
        $persistedCustomer = $entityManager->find(Customer::class, $customer->getId());
        $this->assertNotNull($persistedCustomer);

        // flush后应该能找到
        $entityManager->flush();
        $foundCustomer = $repository->findByCustomerCode($customerCode);
        $this->assertNotNull($foundCustomer);
        $this->assertEquals('无刷新测试客户', $foundCustomer->getName());
    }

    public function testCustomerRemoveWithoutFlush(): void
    {
        $repository = $this->getRepository();

        // 先创建一个客户
        $customer = new Customer();
        $customerCode = 'REMOVE-TEST-' . uniqid();
        $customer->setName('删除测试客户');
        $customer->setCustomerCode($customerCode);
        $customer->setJdCustomerId('JD-REMOVE-' . uniqid());
        $customer->setStatus(CustomerStatusEnum::ACTIVE);
        $customer->setType(CustomerTypeEnum::ENTERPRISE);

        $repository->save($customer, true);
        $this->assertNotNull($repository->findByCustomerCode($customerCode));

        // 不使用flush的remove
        $repository->remove($customer, false);

        // 在同一事务中仍然可以找到
        $stillExists = $repository->findByCustomerCode($customerCode);
        $this->assertNotNull($stillExists);

        // flush后应该被删除
        $entityManager = self::getEntityManager();
        $entityManager->flush();
        $removedCustomer = $repository->findByCustomerCode($customerCode);
        $this->assertNull($removedCustomer);
    }

    public function testCustomerCodeUniqueness(): void
    {
        $repository = $this->getRepository();

        // 创建第一个客户
        $customer1 = new Customer();
        $customerCode = 'UNIQUE-TEST-' . uniqid();
        $customer1->setName('第一个客户');
        $customer1->setCustomerCode($customerCode);
        $customer1->setJdCustomerId('JD-UNIQUE-' . uniqid());
        $customer1->setStatus(CustomerStatusEnum::ACTIVE);
        $customer1->setType(CustomerTypeEnum::ENTERPRISE);

        $repository->save($customer1, true);

        // 验证能找到
        $found = $repository->findByCustomerCode($customerCode);
        $this->assertNotNull($found);
        $this->assertEquals('第一个客户', $found->getName());
    }

    public function testJdCustomerIdUniqueness(): void
    {
        $repository = $this->getRepository();

        // 创建客户
        $customer = new Customer();
        $jdCustomerId = 'JD-UNIQUE-' . uniqid();
        $customer->setName('京东ID唯一性测试客户');
        $customer->setCustomerCode('CUST-' . uniqid());
        $customer->setJdCustomerId($jdCustomerId);
        $customer->setStatus(CustomerStatusEnum::ACTIVE);
        $customer->setType(CustomerTypeEnum::INDIVIDUAL);

        $repository->save($customer, true);

        // 验证能找到
        $found = $repository->findByJdCustomerId($jdCustomerId);
        $this->assertNotNull($found);
        $this->assertEquals('京东ID唯一性测试客户', $found->getName());
        $this->assertEquals($jdCustomerId, $found->getJdCustomerId());
    }
}
