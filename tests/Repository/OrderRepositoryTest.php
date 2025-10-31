<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Tests\Repository;

use JingdongCrmBundle\Entity\Customer;
use JingdongCrmBundle\Entity\Opportunity;
use JingdongCrmBundle\Entity\Order;
use JingdongCrmBundle\Enum\CustomerStatusEnum;
use JingdongCrmBundle\Enum\CustomerTypeEnum;
use JingdongCrmBundle\Enum\OpportunityStageEnum;
use JingdongCrmBundle\Enum\OpportunityStatusEnum;
use JingdongCrmBundle\Enum\OrderStatus;
use JingdongCrmBundle\Repository\OrderRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * OrderRepository测试类
 * @internal
 */
#[CoversClass(OrderRepository::class)]
#[RunTestsInSeparateProcesses]
final class OrderRepositoryTest extends AbstractRepositoryTestCase
{
    protected function createNewEntity(): object
    {
        $customer = $this->createTestCustomer();

        $order = new Order();
        $order->setOrderNumber('ORDER-' . uniqid());
        $order->setCustomer($customer);
        $order->setStatus(OrderStatus::PENDING_PAYMENT);
        $order->setTotalAmount('1000.00');
        $order->setOrderDate(new \DateTimeImmutable());

        return $order;
    }

    protected function getRepository(): OrderRepository
    {
        return self::getService(OrderRepository::class);
    }

    protected function onSetUp(): void
    {
        // 测试设置完成后的操作
    }

    private function createTestCustomer(string $suffix = ''): Customer
    {
        $customer = new Customer();
        $customer->setName('测试客户' . $suffix);
        $customer->setCustomerCode('CUST-' . uniqid() . $suffix);
        $customer->setJdCustomerId('JD-' . uniqid() . $suffix);
        $customer->setStatus(CustomerStatusEnum::ACTIVE);
        $customer->setType(CustomerTypeEnum::ENTERPRISE);

        $entityManager = self::getEntityManager();
        $entityManager->persist($customer);
        $entityManager->flush();

        return $customer;
    }

    private function createTestOpportunity(Customer $customer, string $suffix = ''): Opportunity
    {
        $opportunity = new Opportunity();
        $opportunity->setOpportunityCode('OPP-' . uniqid() . $suffix);
        $opportunity->setName('测试商机' . $suffix);
        $opportunity->setCustomer($customer);
        $opportunity->setStatus(OpportunityStatusEnum::ACTIVE);
        $opportunity->setStage(OpportunityStageEnum::BUSINESS_NEGOTIATION);
        $opportunity->setAmount('50000.00');

        $entityManager = self::getEntityManager();
        $entityManager->persist($opportunity);
        $entityManager->flush();

        return $opportunity;
    }

    public function testFindByOrderNumber(): void
    {
        $repository = $this->getRepository();
        $customer = $this->createTestCustomer();

        // 创建测试订单
        $order = new Order();
        $orderNumber = 'TEST-ORDER-' . uniqid();
        $order->setOrderNumber($orderNumber);
        $order->setCustomer($customer);
        $order->setStatus(OrderStatus::PENDING_PAYMENT);
        $order->setTotalAmount('2500.00');
        $order->setOrderDate(new \DateTimeImmutable());

        $repository->save($order, true);

        // 测试正常查找
        $foundOrder = $repository->findByOrderNumber($orderNumber);
        $this->assertNotNull($foundOrder);
        $this->assertEquals($orderNumber, $foundOrder->getOrderNumber());
        $this->assertEquals('2500.00', $foundOrder->getTotalAmount());

        // 测试查找不存在的订单号
        $notFoundOrder = $repository->findByOrderNumber('NON-EXISTENT-ORDER');
        $this->assertNull($notFoundOrder);
    }

    public function testFindByJdOrderId(): void
    {
        $repository = $this->getRepository();
        $customer = $this->createTestCustomer();

        // 创建测试订单
        $order = new Order();
        $jdOrderId = 'JD-ORDER-' . uniqid();
        $order->setOrderNumber('ORDER-' . uniqid());
        $order->setJdOrderId($jdOrderId);
        $order->setCustomer($customer);
        $order->setStatus(OrderStatus::PAID);
        $order->setTotalAmount('3500.00');
        $order->setOrderDate(new \DateTimeImmutable());

        $repository->save($order, true);

        // 测试正常查找
        $foundOrder = $repository->findByJdOrderId($jdOrderId);
        $this->assertNotNull($foundOrder);
        $this->assertEquals($jdOrderId, $foundOrder->getJdOrderId());
        $this->assertEquals('3500.00', $foundOrder->getTotalAmount());

        // 测试查找不存在的京东订单ID
        $notFoundOrder = $repository->findByJdOrderId('NON-EXISTENT-JD-ORDER');
        $this->assertNull($notFoundOrder);
    }

    public function testFindByCustomer(): void
    {
        $repository = $this->getRepository();
        $customer1 = $this->createTestCustomer('1');
        $customer2 = $this->createTestCustomer('2');

        // 为customer1创建订单
        $order1 = new Order();
        $order1->setOrderNumber('CUST1-ORDER-1-' . uniqid());
        $order1->setCustomer($customer1);
        $order1->setStatus(OrderStatus::PAID);
        $order1->setTotalAmount('1500.00');
        $order1->setOrderDate(new \DateTimeImmutable('-2 days'));

        $order2 = new Order();
        $order2->setOrderNumber('CUST1-ORDER-2-' . uniqid());
        $order2->setCustomer($customer1);
        $order2->setStatus(OrderStatus::COMPLETED);
        $order2->setTotalAmount('2500.00');
        $order2->setOrderDate(new \DateTimeImmutable('-1 day'));

        // 为customer2创建订单
        $order3 = new Order();
        $order3->setOrderNumber('CUST2-ORDER-1-' . uniqid());
        $order3->setCustomer($customer2);
        $order3->setStatus(OrderStatus::SHIPPING);
        $order3->setTotalAmount('3500.00');
        $order3->setOrderDate(new \DateTimeImmutable());

        $repository->save($order1, true);
        $repository->save($order2, true);
        $repository->save($order3, true);

        // 测试查找customer1的订单
        $customer1Orders = $repository->findByCustomer($customer1);
        $this->assertCount(2, $customer1Orders);

        // 验证按订单日期降序排序（最新的在前）
        $this->assertTrue($customer1Orders[0]->getOrderDate() > $customer1Orders[1]->getOrderDate());

        // 验证所有订单都属于customer1
        foreach ($customer1Orders as $order) {
            $this->assertEquals($customer1->getId(), $order->getCustomer()->getId());
        }

        // 测试查找customer2的订单
        $customer2Orders = $repository->findByCustomer($customer2);
        $this->assertCount(1, $customer2Orders);
        $this->assertEquals('3500.00', $customer2Orders[0]->getTotalAmount());
    }

    public function testFindByOpportunity(): void
    {
        $repository = $this->getRepository();
        $customer = $this->createTestCustomer();
        $opportunity1 = $this->createTestOpportunity($customer, '1');
        $opportunity2 = $this->createTestOpportunity($customer, '2');

        // 为opportunity1创建订单
        $order1 = new Order();
        $order1->setOrderNumber('OPP1-ORDER-1-' . uniqid());
        $order1->setCustomer($customer);
        $order1->setOpportunity($opportunity1);
        $order1->setStatus(OrderStatus::PAID);
        $order1->setTotalAmount('5000.00');
        $order1->setOrderDate(new \DateTimeImmutable('-1 day'));

        $order2 = new Order();
        $order2->setOrderNumber('OPP1-ORDER-2-' . uniqid());
        $order2->setCustomer($customer);
        $order2->setOpportunity($opportunity1);
        $order2->setStatus(OrderStatus::COMPLETED);
        $order2->setTotalAmount('7500.00');
        $order2->setOrderDate(new \DateTimeImmutable());

        // 为opportunity2创建订单
        $order3 = new Order();
        $order3->setOrderNumber('OPP2-ORDER-1-' . uniqid());
        $order3->setCustomer($customer);
        $order3->setOpportunity($opportunity2);
        $order3->setStatus(OrderStatus::SHIPPING);
        $order3->setTotalAmount('3000.00');
        $order3->setOrderDate(new \DateTimeImmutable('-2 days'));

        $repository->save($order1, true);
        $repository->save($order2, true);
        $repository->save($order3, true);

        // 测试查找opportunity1的订单
        $opportunity1Orders = $repository->findByOpportunity($opportunity1);
        $this->assertCount(2, $opportunity1Orders);

        // 验证按订单日期降序排序
        $this->assertTrue($opportunity1Orders[0]->getOrderDate() >= $opportunity1Orders[1]->getOrderDate());

        // 验证所有订单都属于opportunity1
        foreach ($opportunity1Orders as $order) {
            $this->assertNotNull($order->getOpportunity());
            $this->assertEquals($opportunity1->getId(), $order->getOpportunity()->getId());
        }

        // 测试查找opportunity2的订单
        $opportunity2Orders = $repository->findByOpportunity($opportunity2);
        $this->assertCount(1, $opportunity2Orders);
        $this->assertEquals('3000.00', $opportunity2Orders[0]->getTotalAmount());
    }

    public function testFindByStatus(): void
    {
        $repository = $this->getRepository();
        $customer = $this->createTestCustomer();

        // 创建不同状态的订单
        $pendingOrder = new Order();
        $pendingOrder->setOrderNumber('PENDING-ORDER-' . uniqid());
        $pendingOrder->setCustomer($customer);
        $pendingOrder->setStatus(OrderStatus::PENDING_PAYMENT);
        $pendingOrder->setTotalAmount('1000.00');
        $pendingOrder->setOrderDate(new \DateTimeImmutable('-1 day'));

        $paidOrder1 = new Order();
        $paidOrder1->setOrderNumber('PAID-ORDER-1-' . uniqid());
        $paidOrder1->setCustomer($customer);
        $paidOrder1->setStatus(OrderStatus::PAID);
        $paidOrder1->setTotalAmount('2000.00');
        $paidOrder1->setOrderDate(new \DateTimeImmutable());

        $paidOrder2 = new Order();
        $paidOrder2->setOrderNumber('PAID-ORDER-2-' . uniqid());
        $paidOrder2->setCustomer($customer);
        $paidOrder2->setStatus(OrderStatus::PAID);
        $paidOrder2->setTotalAmount('3000.00');
        $paidOrder2->setOrderDate(new \DateTimeImmutable('-2 days'));

        $repository->save($pendingOrder, true);
        $repository->save($paidOrder1, true);
        $repository->save($paidOrder2, true);

        // 测试查找待付款订单（过滤我们的测试数据）
        $allPendingOrders = $repository->findByStatus(OrderStatus::PENDING_PAYMENT);
        $pendingOrders = array_filter($allPendingOrders, function ($order) {
            return str_contains($order->getOrderNumber(), 'PENDING-ORDER-');
        });
        $pendingOrders = array_values($pendingOrders);
        $this->assertCount(1, $pendingOrders);
        $this->assertEquals('1000.00', $pendingOrders[0]->getTotalAmount());
        $this->assertEquals(OrderStatus::PENDING_PAYMENT, $pendingOrders[0]->getStatus());

        // 测试查找已付款订单（过滤我们的测试数据）
        $allPaidOrders = $repository->findByStatus(OrderStatus::PAID);
        $paidOrders = array_filter($allPaidOrders, function ($order) {
            return str_contains($order->getOrderNumber(), 'PAID-ORDER-');
        });
        $paidOrders = array_values($paidOrders);
        $this->assertCount(2, $paidOrders);

        // 验证按订单日期降序排序
        $this->assertTrue($paidOrders[0]->getOrderDate() >= $paidOrders[1]->getOrderDate());

        // 验证所有订单都是已付款状态
        foreach ($paidOrders as $order) {
            $this->assertEquals(OrderStatus::PAID, $order->getStatus());
        }
    }

    public function testFindOrdersByDateRange(): void
    {
        $repository = $this->getRepository();
        $customer = $this->createTestCustomer();

        // 创建不同日期的订单
        $oldOrder = new Order();
        $oldOrder->setOrderNumber('OLD-ORDER-' . uniqid());
        $oldOrder->setCustomer($customer);
        $oldOrder->setStatus(OrderStatus::COMPLETED);
        $oldOrder->setTotalAmount('1000.00');
        $oldOrder->setOrderDate(new \DateTimeImmutable('-10 days'));

        $recentOrder1 = new Order();
        $recentOrder1->setOrderNumber('RECENT-ORDER-1-' . uniqid());
        $recentOrder1->setCustomer($customer);
        $recentOrder1->setStatus(OrderStatus::PAID);
        $recentOrder1->setTotalAmount('2000.00');
        $recentOrder1->setOrderDate(new \DateTimeImmutable('-3 days'));

        $recentOrder2 = new Order();
        $recentOrder2->setOrderNumber('RECENT-ORDER-2-' . uniqid());
        $recentOrder2->setCustomer($customer);
        $recentOrder2->setStatus(OrderStatus::SHIPPING);
        $recentOrder2->setTotalAmount('3000.00');
        $recentOrder2->setOrderDate(new \DateTimeImmutable('-1 day'));

        $futureOrder = new Order();
        $futureOrder->setOrderNumber('FUTURE-ORDER-' . uniqid());
        $futureOrder->setCustomer($customer);
        $futureOrder->setStatus(OrderStatus::PENDING_PAYMENT);
        $futureOrder->setTotalAmount('4000.00');
        $futureOrder->setOrderDate(new \DateTimeImmutable('+1 day'));

        $repository->save($oldOrder, true);
        $repository->save($recentOrder1, true);
        $repository->save($recentOrder2, true);
        $repository->save($futureOrder, true);

        // 测试查找最近5天的订单（过滤我们的测试数据）
        $startDate = new \DateTime('-5 days');
        $endDate = new \DateTime();
        $allRecentOrders = $repository->findOrdersByDateRange($startDate, $endDate);
        $recentOrders = array_filter($allRecentOrders, function ($order) {
            return str_contains($order->getOrderNumber(), 'RECENT-ORDER-');
        });
        $recentOrders = array_values($recentOrders);
        $this->assertCount(2, $recentOrders);

        // 验证按日期降序排序
        $this->assertTrue($recentOrders[0]->getOrderDate() >= $recentOrders[1]->getOrderDate());

        // 验证订单都在指定日期范围内
        foreach ($recentOrders as $order) {
            $this->assertGreaterThanOrEqual($startDate, $order->getOrderDate());
            $this->assertLessThanOrEqual($endDate, $order->getOrderDate());
        }
    }

    public function testFindPendingOrders(): void
    {
        $repository = $this->getRepository();
        $customer = $this->createTestCustomer();

        // 创建不同状态的订单
        $pendingOrder1 = new Order();
        $pendingOrder1->setOrderNumber('PENDING-1-' . uniqid());
        $pendingOrder1->setCustomer($customer);
        $pendingOrder1->setStatus(OrderStatus::PENDING_PAYMENT);
        $pendingOrder1->setTotalAmount('1500.00');
        $pendingOrder1->setOrderDate(new \DateTimeImmutable());

        $pendingOrder2 = new Order();
        $pendingOrder2->setOrderNumber('PENDING-2-' . uniqid());
        $pendingOrder2->setCustomer($customer);
        $pendingOrder2->setStatus(OrderStatus::PENDING_PAYMENT);
        $pendingOrder2->setTotalAmount('2500.00');
        $pendingOrder2->setOrderDate(new \DateTimeImmutable('-1 day'));

        $paidOrder = new Order();
        $paidOrder->setOrderNumber('PAID-' . uniqid());
        $paidOrder->setCustomer($customer);
        $paidOrder->setStatus(OrderStatus::PAID);
        $paidOrder->setTotalAmount('3500.00');
        $paidOrder->setOrderDate(new \DateTimeImmutable());

        $repository->save($pendingOrder1, true);
        $repository->save($pendingOrder2, true);
        $repository->save($paidOrder, true);

        // 测试查找待付款订单（过滤我们的测试数据）
        $allPendingOrders = $repository->findPendingOrders();
        $pendingOrders = array_filter($allPendingOrders, function ($order) {
            return str_contains($order->getOrderNumber(), 'PENDING-');
        });
        $pendingOrders = array_values($pendingOrders);
        $this->assertCount(2, $pendingOrders);

        // 验证所有订单都是待付款状态
        foreach ($pendingOrders as $order) {
            $this->assertEquals(OrderStatus::PENDING_PAYMENT, $order->getStatus());
        }
    }

    public function testFindCompletedOrders(): void
    {
        $repository = $this->getRepository();
        $customer = $this->createTestCustomer();

        // 创建不同状态的订单
        $completedOrder1 = new Order();
        $completedOrder1->setOrderNumber('COMPLETED-1-' . uniqid());
        $completedOrder1->setCustomer($customer);
        $completedOrder1->setStatus(OrderStatus::COMPLETED);
        $completedOrder1->setTotalAmount('2000.00');
        $completedOrder1->setOrderDate(new \DateTimeImmutable());

        $completedOrder2 = new Order();
        $completedOrder2->setOrderNumber('COMPLETED-2-' . uniqid());
        $completedOrder2->setCustomer($customer);
        $completedOrder2->setStatus(OrderStatus::COMPLETED);
        $completedOrder2->setTotalAmount('3000.00');
        $completedOrder2->setOrderDate(new \DateTimeImmutable('-1 day'));

        $shippingOrder = new Order();
        $shippingOrder->setOrderNumber('SHIPPING-' . uniqid());
        $shippingOrder->setCustomer($customer);
        $shippingOrder->setStatus(OrderStatus::SHIPPING);
        $shippingOrder->setTotalAmount('1500.00');
        $shippingOrder->setOrderDate(new \DateTimeImmutable());

        $repository->save($completedOrder1, true);
        $repository->save($completedOrder2, true);
        $repository->save($shippingOrder, true);

        // 测试查找已完成订单（过滤我们的测试数据）
        $allCompletedOrders = $repository->findCompletedOrders();
        $completedOrders = array_filter($allCompletedOrders, function ($order) {
            return str_contains($order->getOrderNumber(), 'COMPLETED-');
        });
        $completedOrders = array_values($completedOrders);
        $this->assertCount(2, $completedOrders);

        // 验证所有订单都是已完成状态
        foreach ($completedOrders as $order) {
            $this->assertEquals(OrderStatus::COMPLETED, $order->getStatus());
        }
    }

    public function testGetTotalAmountByCustomer(): void
    {
        $repository = $this->getRepository();
        $customer = $this->createTestCustomer();

        // 创建不同状态的订单
        $paidOrder = new Order();
        $paidOrder->setOrderNumber('PAID-ORDER-' . uniqid());
        $paidOrder->setCustomer($customer);
        $paidOrder->setStatus(OrderStatus::PAID);
        $paidOrder->setTotalAmount('1000.00');
        $paidOrder->setOrderDate(new \DateTimeImmutable());

        $shippingOrder = new Order();
        $shippingOrder->setOrderNumber('SHIPPING-ORDER-' . uniqid());
        $shippingOrder->setCustomer($customer);
        $shippingOrder->setStatus(OrderStatus::SHIPPING);
        $shippingOrder->setTotalAmount('2000.00');
        $shippingOrder->setOrderDate(new \DateTimeImmutable());

        $completedOrder = new Order();
        $completedOrder->setOrderNumber('COMPLETED-ORDER-' . uniqid());
        $completedOrder->setCustomer($customer);
        $completedOrder->setStatus(OrderStatus::COMPLETED);
        $completedOrder->setTotalAmount('3000.00');
        $completedOrder->setOrderDate(new \DateTimeImmutable());

        // 创建不计入总金额的订单（待付款）
        $pendingOrder = new Order();
        $pendingOrder->setOrderNumber('PENDING-ORDER-' . uniqid());
        $pendingOrder->setCustomer($customer);
        $pendingOrder->setStatus(OrderStatus::PENDING_PAYMENT);
        $pendingOrder->setTotalAmount('5000.00');
        $pendingOrder->setOrderDate(new \DateTimeImmutable());

        // 创建取消的订单（不计入总金额）
        $cancelledOrder = new Order();
        $cancelledOrder->setOrderNumber('CANCELLED-ORDER-' . uniqid());
        $cancelledOrder->setCustomer($customer);
        $cancelledOrder->setStatus(OrderStatus::CANCELLED);
        $cancelledOrder->setTotalAmount('1500.00');
        $cancelledOrder->setOrderDate(new \DateTimeImmutable());

        $repository->save($paidOrder, true);
        $repository->save($shippingOrder, true);
        $repository->save($completedOrder, true);
        $repository->save($pendingOrder, true);
        $repository->save($cancelledOrder, true);

        // 测试计算客户总金额（只包括已付款、配送中、已完成的订单）
        $totalAmount = $repository->getTotalAmountByCustomer($customer);
        $this->assertEquals('6000.00', $totalAmount); // 1000 + 2000 + 3000

        // 测试没有有效订单的客户
        $customerWithoutOrders = $this->createTestCustomer('-no-orders');
        $zeroAmount = $repository->getTotalAmountByCustomer($customerWithoutOrders);
        $this->assertEquals('0.00', $zeroAmount);
    }

    public function testOrderSaveAndRemove(): void
    {
        $repository = $this->getRepository();
        $customer = $this->createTestCustomer();

        // 测试save方法
        $order = new Order();
        $orderNumber = 'SAVE-TEST-' . uniqid();
        $order->setOrderNumber($orderNumber);
        $order->setCustomer($customer);
        $order->setStatus(OrderStatus::PENDING_PAYMENT);
        $order->setTotalAmount('1800.00');
        $order->setOrderDate(new \DateTimeImmutable());

        // 测试不flush的save
        $repository->save($order, false);
        $foundBeforeFlush = $repository->findByOrderNumber($orderNumber);
        $this->assertNull($foundBeforeFlush); // 还未flush，不应该找到

        // 手动flush
        $entityManager = self::getEntityManager();
        $entityManager->flush();
        $foundAfterFlush = $repository->findByOrderNumber($orderNumber);
        $this->assertNotNull($foundAfterFlush);
        $this->assertEquals($orderNumber, $foundAfterFlush->getOrderNumber());

        // 测试带flush的save
        $order2 = new Order();
        $orderNumber2 = 'SAVE-TEST-2-' . uniqid();
        $order2->setOrderNumber($orderNumber2);
        $order2->setCustomer($customer);
        $order2->setStatus(OrderStatus::PAID);
        $order2->setTotalAmount('2800.00');
        $order2->setOrderDate(new \DateTimeImmutable());

        $repository->save($order2, true);
        $foundImmediately = $repository->findByOrderNumber($orderNumber2);
        $this->assertNotNull($foundImmediately);
        $this->assertEquals($orderNumber2, $foundImmediately->getOrderNumber());

        // 测试remove方法
        $repository->remove($foundImmediately, true);
        $removedOrder = $repository->findByOrderNumber($orderNumber2);
        $this->assertNull($removedOrder);
    }
}
