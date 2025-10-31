<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Tests\Entity;

use JingdongCrmBundle\Entity\Customer;
use JingdongCrmBundle\Enum\CustomerStatusEnum;
use JingdongCrmBundle\Enum\CustomerTypeEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Customer::class)]
final class CustomerTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Customer();
    }

    /**
     * @return array<array{string, mixed}>
     */
    public static function propertiesProvider(): array
    {
        return [
            ['customerCode', 'CUST001'],
            ['name', 'Test Customer'],
            ['type', CustomerTypeEnum::ENTERPRISE],
            ['email', 'test@example.com'],
            ['phone', '123-456-7890'],
            ['address', '123 Main St'],
            ['status', CustomerStatusEnum::SUSPENDED],
            ['jdCustomerId', 'JD123456'],
        ];
    }

    public function testDefaultValues(): void
    {
        $customer = new Customer();

        self::assertSame(CustomerTypeEnum::INDIVIDUAL, $customer->getType());
        self::assertSame(CustomerStatusEnum::ACTIVE, $customer->getStatus());
        self::assertSame(0, $customer->getId());
    }

    public function testGetSetCustomerCode(): void
    {
        $customer = new Customer();
        $customerCode = 'CUST001';

        $customer->setCustomerCode($customerCode);
        self::assertSame($customerCode, $customer->getCustomerCode());
    }

    public function testGetSetName(): void
    {
        $customer = new Customer();
        $name = 'Test Customer';

        $customer->setName($name);
        self::assertSame($name, $customer->getName());
    }

    public function testGetSetType(): void
    {
        $customer = new Customer();

        self::assertSame(CustomerTypeEnum::INDIVIDUAL, $customer->getType());

        $customer->setType(CustomerTypeEnum::ENTERPRISE);
        self::assertSame(CustomerTypeEnum::ENTERPRISE, $customer->getType());
    }

    public function testGetSetEmail(): void
    {
        $customer = new Customer();
        $email = 'test@example.com';

        $customer->setEmail($email);
        self::assertSame($email, $customer->getEmail());

        $customer->setEmail(null);
        self::assertNull($customer->getEmail());
    }

    public function testGetSetPhone(): void
    {
        $customer = new Customer();
        $phone = '123-456-7890';

        $customer->setPhone($phone);
        self::assertSame($phone, $customer->getPhone());

        $customer->setPhone(null);
        self::assertNull($customer->getPhone());
    }

    public function testGetSetAddress(): void
    {
        $customer = new Customer();
        $address = '123 Main St';

        $customer->setAddress($address);
        self::assertSame($address, $customer->getAddress());

        $customer->setAddress(null);
        self::assertNull($customer->getAddress());
    }

    public function testGetSetStatus(): void
    {
        $customer = new Customer();

        self::assertSame(CustomerStatusEnum::ACTIVE, $customer->getStatus());

        $customer->setStatus(CustomerStatusEnum::SUSPENDED);
        self::assertSame(CustomerStatusEnum::SUSPENDED, $customer->getStatus());
    }

    public function testGetSetJdCustomerId(): void
    {
        $customer = new Customer();
        $jdCustomerId = 'JD123456';

        $customer->setJdCustomerId($jdCustomerId);
        self::assertSame($jdCustomerId, $customer->getJdCustomerId());
    }

    public function testGetId(): void
    {
        $customer = new Customer();

        self::assertSame(0, $customer->getId());
    }

    public function testToString(): void
    {
        $customer = new Customer();
        $customer->setName('Test Customer');
        $customer->setCustomerCode('CUST001');

        // getId() returns 0 for new entities
        self::assertSame('Customer[0]: Test Customer (CUST001)', (string) $customer);
    }

    public function testToStringWithDefaults(): void
    {
        $customer = new Customer();

        // Test fallback values in __toString
        self::assertSame('Customer[0]: Unnamed (No Code)', (string) $customer);
    }
}
