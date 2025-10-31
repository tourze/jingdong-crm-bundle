<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Tests\Repository;

use JingdongCrmBundle\Entity\Contact;
use JingdongCrmBundle\Entity\Customer;
use JingdongCrmBundle\Enum\ContactStatusEnum;
use JingdongCrmBundle\Enum\CustomerStatusEnum;
use JingdongCrmBundle\Enum\CustomerTypeEnum;
use JingdongCrmBundle\Repository\ContactRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * ContactRepository测试类
 * @internal
 */
#[CoversClass(ContactRepository::class)]
#[RunTestsInSeparateProcesses]
final class ContactRepositoryTest extends AbstractRepositoryTestCase
{
    protected function createNewEntity(): object
    {
        $customer = $this->createTestCustomer('-for-new-entity');

        $contact = new Contact();
        $contact->setName('测试联系人');
        $contact->setEmail('test@example.com');
        $contact->setStatus(ContactStatusEnum::ACTIVE);
        $contact->setCustomer($customer);

        return $contact;
    }

    protected function getRepository(): ContactRepository
    {
        return self::getService(ContactRepository::class);
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

    public function testFindByCustomer(): void
    {
        $repository = $this->getRepository();
        $customer1 = $this->createTestCustomer('1');
        $customer2 = $this->createTestCustomer('2');

        // 为customer1创建联系人
        $contact1 = new Contact();
        $contact1->setName('客户1主联系人');
        $contact1->setEmail('primary1@example.com');
        $contact1->setCustomer($customer1);
        $contact1->setIsPrimary(true);
        $contact1->setStatus(ContactStatusEnum::ACTIVE);

        $contact2 = new Contact();
        $contact2->setName('客户1副联系人');
        $contact2->setEmail('secondary1@example.com');
        $contact2->setCustomer($customer1);
        $contact2->setIsPrimary(false);
        $contact2->setStatus(ContactStatusEnum::ACTIVE);

        // 为customer2创建联系人
        $contact3 = new Contact();
        $contact3->setName('客户2联系人');
        $contact3->setEmail('contact2@example.com');
        $contact3->setCustomer($customer2);
        $contact3->setStatus(ContactStatusEnum::ACTIVE);

        $repository->save($contact1, true);
        $repository->save($contact2, true);
        $repository->save($contact3, true);

        // 测试查找customer1的联系人
        $customer1Contacts = $repository->findByCustomer($customer1);
        $this->assertCount(2, $customer1Contacts);

        // 验证排序：主联系人在前，然后按姓名排序
        $this->assertEquals('客户1主联系人', $customer1Contacts[0]->getName());
        $this->assertTrue($customer1Contacts[0]->isPrimary());
        $this->assertEquals('客户1副联系人', $customer1Contacts[1]->getName());
        $this->assertFalse($customer1Contacts[1]->isPrimary());

        // 测试查找customer2的联系人
        $customer2Contacts = $repository->findByCustomer($customer2);
        $this->assertCount(1, $customer2Contacts);
        $this->assertEquals('客户2联系人', $customer2Contacts[0]->getName());
    }

    public function testFindPrimaryContactByCustomer(): void
    {
        $repository = $this->getRepository();
        $customer = $this->createTestCustomer();

        // 创建主联系人
        $primaryContact = new Contact();
        $primaryContact->setName('主联系人');
        $primaryContact->setEmail('primary@example.com');
        $primaryContact->setCustomer($customer);
        $primaryContact->setIsPrimary(true);
        $primaryContact->setStatus(ContactStatusEnum::ACTIVE);

        // 创建非主联系人
        $secondaryContact = new Contact();
        $secondaryContact->setName('副联系人');
        $secondaryContact->setEmail('secondary@example.com');
        $secondaryContact->setCustomer($customer);
        $secondaryContact->setIsPrimary(false);
        $secondaryContact->setStatus(ContactStatusEnum::ACTIVE);

        $repository->save($primaryContact, true);
        $repository->save($secondaryContact, true);

        // 测试查找主联系人
        $foundPrimary = $repository->findPrimaryContactByCustomer($customer);
        $this->assertNotNull($foundPrimary);
        $this->assertEquals('主联系人', $foundPrimary->getName());
        $this->assertTrue($foundPrimary->isPrimary());

        // 测试没有主联系人的情况
        $customerWithoutPrimary = $this->createTestCustomer('-no-primary');
        $noPrimary = $repository->findPrimaryContactByCustomer($customerWithoutPrimary);
        $this->assertNull($noPrimary);
    }

    public function testFindActiveContacts(): void
    {
        $repository = $this->getRepository();
        $customer = $this->createTestCustomer('-for-active-test');

        // 创建活跃联系人
        $activeContact1 = new Contact();
        $activeContact1->setName('找活跃A');
        $activeContact1->setEmail('find-active1@example.com');
        $activeContact1->setCustomer($customer);
        $activeContact1->setStatus(ContactStatusEnum::ACTIVE);

        $activeContact2 = new Contact();
        $activeContact2->setName('找活跃B');
        $activeContact2->setEmail('find-active2@example.com');
        $activeContact2->setCustomer($customer);
        $activeContact2->setStatus(ContactStatusEnum::ACTIVE);

        // 创建非活跃联系人
        $inactiveContact = new Contact();
        $inactiveContact->setName('非活跃联系人');
        $inactiveContact->setEmail('inactive-test@example.com');
        $inactiveContact->setCustomer($customer);
        $inactiveContact->setStatus(ContactStatusEnum::INACTIVE);

        $repository->save($activeContact1, true);
        $repository->save($activeContact2, true);
        $repository->save($inactiveContact, true);

        // 测试查找活跃联系人（使用LIKE查询来过滤出我们想要的结果）
        $activeContacts = $repository->findActiveContacts();

        // 过滤出我们创建的测试数据
        $ourActiveContacts = array_filter($activeContacts, function ($contact) {
            return str_contains($contact->getName(), '找活跃');
        });

        $this->assertCount(2, $ourActiveContacts);

        // 重新索引数组以便比较
        $ourActiveContacts = array_values($ourActiveContacts);

        // 验证按姓名排序
        $this->assertEquals('找活跃A', $ourActiveContacts[0]->getName());
        $this->assertEquals('找活跃B', $ourActiveContacts[1]->getName());

        // 验证所有联系人都是活跃状态
        foreach ($ourActiveContacts as $contact) {
            $this->assertEquals(ContactStatusEnum::ACTIVE, $contact->getStatus());
        }
    }

    public function testFindByStatus(): void
    {
        $repository = $this->getRepository();
        $customer = $this->createTestCustomer('-for-status-test');

        // 创建不同状态的联系人
        $activeContact = new Contact();
        $activeContact->setName('状态测试活跃联系人');
        $activeContact->setEmail('status-active@example.com');
        $activeContact->setCustomer($customer);
        $activeContact->setStatus(ContactStatusEnum::ACTIVE);

        $inactiveContact = new Contact();
        $inactiveContact->setName('状态测试非活跃联系人');
        $inactiveContact->setEmail('status-inactive@example.com');
        $inactiveContact->setCustomer($customer);
        $inactiveContact->setStatus(ContactStatusEnum::INACTIVE);

        $repository->save($activeContact, true);
        $repository->save($inactiveContact, true);

        // 测试按状态查找活跃联系人
        $activeContacts = $repository->findByStatus(ContactStatusEnum::ACTIVE);
        $ourActiveContacts = array_filter($activeContacts, function ($contact) {
            return str_contains($contact->getName(), '状态测试活跃');
        });
        $this->assertCount(1, $ourActiveContacts);
        $this->assertEquals('状态测试活跃联系人', reset($ourActiveContacts)->getName());

        // 测试按状态查找非活跃联系人
        $inactiveContacts = $repository->findByStatus(ContactStatusEnum::INACTIVE);
        $ourInactiveContacts = array_filter($inactiveContacts, function ($contact) {
            return str_contains($contact->getName(), '状态测试非活跃');
        });
        $this->assertCount(1, $ourInactiveContacts);
        $this->assertEquals('状态测试非活跃联系人', reset($ourInactiveContacts)->getName());
    }

    public function testFindByEmail(): void
    {
        $repository = $this->getRepository();
        $customer = $this->createTestCustomer();

        $contact = new Contact();
        $email = 'unique@example.com';
        $contact->setName('邮箱测试联系人');
        $contact->setEmail($email);
        $contact->setCustomer($customer);
        $contact->setStatus(ContactStatusEnum::ACTIVE);

        $repository->save($contact, true);

        // 测试按邮箱查找
        $foundContact = $repository->findByEmail($email);
        $this->assertNotNull($foundContact);
        $this->assertEquals('邮箱测试联系人', $foundContact->getName());
        $this->assertEquals($email, $foundContact->getEmail());

        // 测试查找不存在的邮箱
        $notFound = $repository->findByEmail('nonexistent@example.com');
        $this->assertNull($notFound);
    }

    public function testFindByPhone(): void
    {
        $repository = $this->getRepository();
        $customer = $this->createTestCustomer('-phone-test');

        // 创建有固话的联系人
        $contactWithPhone = new Contact();
        $contactWithPhone->setName('有固话的联系人');
        $contactWithPhone->setEmail('phone-test@example.com');
        $contactWithPhone->setPhone('010-88888888');
        $contactWithPhone->setCustomer($customer);
        $contactWithPhone->setStatus(ContactStatusEnum::ACTIVE);

        // 创建有手机的联系人
        $contactWithMobile = new Contact();
        $contactWithMobile->setName('有手机的联系人');
        $contactWithMobile->setEmail('mobile-test@example.com');
        $contactWithMobile->setMobile('18800188001');
        $contactWithMobile->setCustomer($customer);
        $contactWithMobile->setStatus(ContactStatusEnum::ACTIVE);

        $repository->save($contactWithPhone, true);
        $repository->save($contactWithMobile, true);

        // 测试按固话查找
        $foundByPhone = $repository->findByPhone('010-88888888');
        $this->assertNotNull($foundByPhone);
        $this->assertEquals('有固话的联系人', $foundByPhone->getName());

        // 测试按手机查找
        $foundByMobile = $repository->findByPhone('18800188001');
        $this->assertNotNull($foundByMobile);
        $this->assertEquals('有手机的联系人', $foundByMobile->getName());

        // 测试查找不存在的电话
        $notFound = $repository->findByPhone('99999999999');
        $this->assertNull($notFound);
    }

    public function testContactSaveAndRemove(): void
    {
        $repository = $this->getRepository();
        $customer = $this->createTestCustomer();

        // 测试save方法
        $contact = new Contact();
        $contact->setName('保存测试联系人');
        $contact->setEmail('save-test@example.com');
        $contact->setCustomer($customer);
        $contact->setStatus(ContactStatusEnum::ACTIVE);

        // 测试不flush的save
        $repository->save($contact, false);
        $foundBeforeFlush = $repository->findByEmail('save-test@example.com');
        $this->assertNull($foundBeforeFlush);

        // 手动flush
        $entityManager = self::getEntityManager();
        $entityManager->flush();
        $foundAfterFlush = $repository->findByEmail('save-test@example.com');
        $this->assertNotNull($foundAfterFlush);
        $this->assertEquals('保存测试联系人', $foundAfterFlush->getName());

        // 测试带flush的save
        $contact2 = new Contact();
        $contact2->setName('保存测试联系人2');
        $contact2->setEmail('save-test2@example.com');
        $contact2->setCustomer($customer);
        $contact2->setStatus(ContactStatusEnum::ACTIVE);

        $repository->save($contact2, true);
        $foundImmediately = $repository->findByEmail('save-test2@example.com');
        $this->assertNotNull($foundImmediately);
        $this->assertEquals('保存测试联系人2', $foundImmediately->getName());

        // 测试remove方法
        $repository->remove($foundImmediately, true);
        $removedContact = $repository->findByEmail('save-test2@example.com');
        $this->assertNull($removedContact);
    }
}
