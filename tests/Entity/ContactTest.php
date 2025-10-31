<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Tests\Entity;

use JingdongCrmBundle\Entity\Contact;
use JingdongCrmBundle\Entity\Customer;
use JingdongCrmBundle\Enum\ContactStatusEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Contact::class)]
final class ContactTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Contact();
    }

    /**
     * @return array<array{string, mixed}>
     */
    public static function propertiesProvider(): array
    {
        return [
            ['name', 'John Doe'],
            ['title', 'Manager'],
            ['email', 'john@example.com'],
            ['phone', '123-456-7890'],
            ['mobile', '987-654-3210'],
            ['isPrimary', true],
            ['status', ContactStatusEnum::INACTIVE],
        ];
    }

    public function testConstruct(): void
    {
        $contact = new Contact();

        self::assertSame(ContactStatusEnum::ACTIVE, $contact->getStatus());
        self::assertFalse($contact->isPrimary());
    }

    public function testSetCustomerReturnsVoid(): void
    {
        $this->expectNotToPerformAssertions();

        $contact = new Contact();
        $customer = new Customer();
        $contact->setCustomer($customer);

        // void方法无需断言，方法执行不抛异常即为成功
    }

    public function testGetSetCustomer(): void
    {
        $contact = new Contact();
        $customer = new Customer();

        $contact->setCustomer($customer);
        self::assertSame($customer, $contact->getCustomer());
    }

    public function testSetNameReturnsVoid(): void
    {
        $this->expectNotToPerformAssertions();

        $contact = new Contact();
        $contact->setName('John Doe');

        // void方法无需断言，方法执行不抛异常即为成功
    }

    public function testGetSetName(): void
    {
        $contact = new Contact();
        $name = 'John Doe';

        $contact->setName($name);
        self::assertSame($name, $contact->getName());
    }

    public function testSetTitleReturnsVoid(): void
    {
        $this->expectNotToPerformAssertions();

        $contact = new Contact();
        $contact->setTitle('Manager');

        // void方法无需断言，方法执行不抛异常即为成功
    }

    public function testGetSetTitle(): void
    {
        $contact = new Contact();
        $title = 'Manager';

        $contact->setTitle($title);
        self::assertSame($title, $contact->getTitle());

        $contact->setTitle(null);
        self::assertNull($contact->getTitle());
    }

    public function testSetEmailReturnsVoid(): void
    {
        $this->expectNotToPerformAssertions();

        $contact = new Contact();
        $contact->setEmail('john@example.com');

        // void方法无需断言，方法执行不抛异常即为成功
    }

    public function testGetSetEmail(): void
    {
        $contact = new Contact();
        $email = 'john@example.com';

        $contact->setEmail($email);
        self::assertSame($email, $contact->getEmail());

        $contact->setEmail(null);
        self::assertNull($contact->getEmail());
    }

    public function testSetPhoneReturnsVoid(): void
    {
        $this->expectNotToPerformAssertions();

        $contact = new Contact();
        $contact->setPhone('123-456-7890');

        // void方法无需断言，方法执行不抛异常即为成功
    }

    public function testGetSetPhone(): void
    {
        $contact = new Contact();
        $phone = '123-456-7890';

        $contact->setPhone($phone);
        self::assertSame($phone, $contact->getPhone());

        $contact->setPhone(null);
        self::assertNull($contact->getPhone());
    }

    public function testSetMobileReturnsVoid(): void
    {
        $this->expectNotToPerformAssertions();

        $contact = new Contact();
        $contact->setMobile('987-654-3210');

        // void方法无需断言，方法执行不抛异常即为成功
    }

    public function testGetSetMobile(): void
    {
        $contact = new Contact();
        $mobile = '987-654-3210';

        $contact->setMobile($mobile);
        self::assertSame($mobile, $contact->getMobile());

        $contact->setMobile(null);
        self::assertNull($contact->getMobile());
    }

    public function testSetIsPrimaryReturnsVoid(): void
    {
        $this->expectNotToPerformAssertions();

        $contact = new Contact();
        $contact->setIsPrimary(true);

        // void方法无需断言，方法执行不抛异帰吳即为成功
    }

    public function testGetSetIsPrimary(): void
    {
        $contact = new Contact();

        self::assertFalse($contact->isPrimary());

        $contact->setIsPrimary(true);
        self::assertTrue($contact->isPrimary());

        $contact->setIsPrimary(false);
        self::assertFalse($contact->isPrimary());
    }

    public function testSetStatusReturnsVoid(): void
    {
        $this->expectNotToPerformAssertions();

        $contact = new Contact();
        $contact->setStatus(ContactStatusEnum::INACTIVE);

        // void方法无需断言，方法执行不抛异常即为成功
    }

    public function testGetSetStatus(): void
    {
        $contact = new Contact();

        self::assertSame(ContactStatusEnum::ACTIVE, $contact->getStatus());

        $contact->setStatus(ContactStatusEnum::INACTIVE);
        self::assertSame(ContactStatusEnum::INACTIVE, $contact->getStatus());
    }

    public function testGetId(): void
    {
        $contact = new Contact();

        self::assertNull($contact->getId());
    }

    public function testToString(): void
    {
        $contact = new Contact();
        $contact->setName('John Doe');
        $contact->setTitle('Manager');

        self::assertSame('John Doe (Manager)', (string) $contact);
    }

    public function testToStringWithoutTitle(): void
    {
        $contact = new Contact();
        $contact->setName('John Doe');

        self::assertSame('John Doe (无职位)', (string) $contact);
    }
}
