<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Tests\Entity;

use JingdongCrmBundle\Entity\Customer;
use JingdongCrmBundle\Entity\Opportunity;
use JingdongCrmBundle\Entity\Order;
use JingdongCrmBundle\Enum\OrderStatus;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Order::class)]
final class OrderTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Order();
    }

    /**
     * @return array<array{string, mixed}>
     */
    public static function propertiesProvider(): array
    {
        return [
            ['orderNumber', 'ORD001'],
            ['status', OrderStatus::PAID],
            ['totalAmount', '1000.00'],
            ['paidAmount', '500.00'],
            ['discountAmount', '100.00'],
            ['orderDate', new \DateTimeImmutable('2023-12-31')],
            ['paymentMethod', 'credit_card'],
            ['shippingAddress', '123 Main St'],
            ['jdOrderId', 'JD123456'],
            ['notes', 'Test notes'],
        ];
    }

    public function testConstruct(): void
    {
        $order = new Order();

        self::assertSame(OrderStatus::PENDING_PAYMENT, $order->getStatus());
        self::assertSame('0.00', $order->getPaidAmount());
        self::assertSame('0.00', $order->getDiscountAmount());
        self::assertLessThanOrEqual(new \DateTimeImmutable(), $order->getOrderDate());
        self::assertSame(0, $order->getId());
    }

    public function testGetSetOrderNumber(): void
    {
        $order = new Order();
        $orderNumber = 'ORD001';

        $order->setOrderNumber($orderNumber);
        self::assertSame($orderNumber, $order->getOrderNumber());
    }

    public function testGetSetCustomer(): void
    {
        $order = new Order();
        $customer = new Customer();

        $order->setCustomer($customer);
        self::assertSame($customer, $order->getCustomer());
    }

    public function testGetSetOpportunity(): void
    {
        $order = new Order();
        $opportunity = new Opportunity();

        $order->setOpportunity($opportunity);
        self::assertSame($opportunity, $order->getOpportunity());

        $order->setOpportunity(null);
        self::assertNull($order->getOpportunity());
    }

    public function testGetSetStatus(): void
    {
        $order = new Order();

        self::assertSame(OrderStatus::PENDING_PAYMENT, $order->getStatus());

        $order->setStatus(OrderStatus::PAID);
        self::assertSame(OrderStatus::PAID, $order->getStatus());
    }

    public function testGetSetTotalAmount(): void
    {
        $order = new Order();
        $totalAmount = '1000.00';

        $order->setTotalAmount($totalAmount);
        self::assertSame($totalAmount, $order->getTotalAmount());
    }

    public function testGetSetPaidAmount(): void
    {
        $order = new Order();

        self::assertSame('0.00', $order->getPaidAmount());

        $order->setPaidAmount('500.00');
        self::assertSame('500.00', $order->getPaidAmount());
    }

    public function testGetSetDiscountAmount(): void
    {
        $order = new Order();

        self::assertSame('0.00', $order->getDiscountAmount());

        $order->setDiscountAmount('100.00');
        self::assertSame('100.00', $order->getDiscountAmount());
    }

    public function testGetSetOrderDate(): void
    {
        $order = new Order();
        $date = new \DateTimeImmutable('2023-12-31');

        $order->setOrderDate($date);
        self::assertSame($date, $order->getOrderDate());
    }

    public function testGetSetPaymentMethod(): void
    {
        $order = new Order();
        $paymentMethod = 'credit_card';

        $order->setPaymentMethod($paymentMethod);
        self::assertSame($paymentMethod, $order->getPaymentMethod());

        $order->setPaymentMethod(null);
        self::assertNull($order->getPaymentMethod());
    }

    public function testGetSetShippingAddress(): void
    {
        $order = new Order();
        $shippingAddress = '123 Main St';

        $order->setShippingAddress($shippingAddress);
        self::assertSame($shippingAddress, $order->getShippingAddress());

        $order->setShippingAddress(null);
        self::assertNull($order->getShippingAddress());
    }

    public function testGetSetJdOrderId(): void
    {
        $order = new Order();
        $jdOrderId = 'JD123456';

        $order->setJdOrderId($jdOrderId);
        self::assertSame($jdOrderId, $order->getJdOrderId());

        $order->setJdOrderId(null);
        self::assertNull($order->getJdOrderId());
    }

    public function testGetSetNotes(): void
    {
        $order = new Order();
        $notes = 'Test notes';

        $order->setNotes($notes);
        self::assertSame($notes, $order->getNotes());

        $order->setNotes(null);
        self::assertNull($order->getNotes());
    }

    public function testGetId(): void
    {
        $order = new Order();

        self::assertSame(0, $order->getId());
    }

    public function testToString(): void
    {
        $order = new Order();
        $order->setOrderNumber('ORD001');
        $order->setTotalAmount('1000.00');
        $order->setStatus(OrderStatus::PAID);

        $expectedString = sprintf(
            'Order[0]: ORD001 - %s (¥1000.00)',
            $order->getStatus()->getLabel()
        );
        self::assertSame($expectedString, (string) $order);
    }

    public function testToStringWithDefaults(): void
    {
        $order = new Order();

        $expectedString = sprintf(
            'Order[0]: No Number - %s (¥0.00)',
            $order->getStatus()->getLabel()
        );
        self::assertSame($expectedString, (string) $order);
    }
}
