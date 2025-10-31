<?php

declare(strict_types=1);

namespace JingdongCrmBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use JingdongCrmBundle\Entity\Contact;
use JingdongCrmBundle\Entity\Customer;
use JingdongCrmBundle\Enum\ContactStatusEnum;

/**
 * 联系人测试数据
 */
class ContactFixtures extends Fixture implements DependentFixtureInterface
{
    public const CONTACT_TECH_CORP_CEO = 'jingdong-crm-contact-tech-corp-ceo';
    public const CONTACT_TECH_CORP_CTO = 'jingdong-crm-contact-tech-corp-cto';
    public const CONTACT_DIGITAL_SOLUTIONS_MD = 'jingdong-crm-contact-digital-solutions-md';
    public const CONTACT_DIGITAL_SOLUTIONS_PM = 'jingdong-crm-contact-digital-solutions-pm';
    public const CONTACT_STARTUP_FOUNDER = 'jingdong-crm-contact-startup-founder';

    public function load(ObjectManager $manager): void
    {
        // 科技公司CEO - 主要联系人
        $techCorpCEO = new Contact();
        $techCorpCEO->setCustomer($this->getReference(CustomerFixtures::CUSTOMER_TECH_CORP, Customer::class));
        $techCorpCEO->setName('王志强');
        $techCorpCEO->setTitle('首席执行官');
        $techCorpCEO->setEmail('ceo@tech-corp.com');
        $techCorpCEO->setPhone('010-12345678');
        $techCorpCEO->setMobile('138-1234-5678');
        $techCorpCEO->setIsPrimary(true);
        $techCorpCEO->setStatus(ContactStatusEnum::ACTIVE);

        $manager->persist($techCorpCEO);
        $this->addReference(self::CONTACT_TECH_CORP_CEO, $techCorpCEO);

        // 科技公司CTO
        $techCorpCTO = new Contact();
        $techCorpCTO->setCustomer($this->getReference(CustomerFixtures::CUSTOMER_TECH_CORP, Customer::class));
        $techCorpCTO->setName('李技术');
        $techCorpCTO->setTitle('首席技术官');
        $techCorpCTO->setEmail('cto@tech-corp.com');
        $techCorpCTO->setPhone('010-12345679');
        $techCorpCTO->setMobile('138-1234-5679');
        $techCorpCTO->setIsPrimary(false);
        $techCorpCTO->setStatus(ContactStatusEnum::ACTIVE);

        $manager->persist($techCorpCTO);
        $this->addReference(self::CONTACT_TECH_CORP_CTO, $techCorpCTO);

        // 数字化解决方案公司总经理 - 主要联系人
        $digitalMD = new Contact();
        $digitalMD->setCustomer($this->getReference(CustomerFixtures::CUSTOMER_DIGITAL_SOLUTIONS, Customer::class));
        $digitalMD->setName('陈总经理');
        $digitalMD->setTitle('总经理');
        $digitalMD->setEmail('md@digital-solutions.com');
        $digitalMD->setPhone('021-87654321');
        $digitalMD->setMobile('139-8765-4321');
        $digitalMD->setIsPrimary(true);
        $digitalMD->setStatus(ContactStatusEnum::ACTIVE);

        $manager->persist($digitalMD);
        $this->addReference(self::CONTACT_DIGITAL_SOLUTIONS_MD, $digitalMD);

        // 数字化解决方案公司项目经理
        $digitalPM = new Contact();
        $digitalPM->setCustomer($this->getReference(CustomerFixtures::CUSTOMER_DIGITAL_SOLUTIONS, Customer::class));
        $digitalPM->setName('赵项目');
        $digitalPM->setTitle('项目经理');
        $digitalPM->setEmail('pm@digital-solutions.com');
        $digitalPM->setPhone('021-87654322');
        $digitalPM->setMobile('139-8765-4322');
        $digitalPM->setIsPrimary(false);
        $digitalPM->setStatus(ContactStatusEnum::ACTIVE);

        $manager->persist($digitalPM);
        $this->addReference(self::CONTACT_DIGITAL_SOLUTIONS_PM, $digitalPM);

        // 创业公司创始人 - 主要联系人
        $startupFounder = new Contact();
        $startupFounder->setCustomer($this->getReference(CustomerFixtures::CUSTOMER_STARTUP_INC, Customer::class));
        $startupFounder->setName('孙创始人');
        $startupFounder->setTitle('创始人兼CEO');
        $startupFounder->setEmail('founder@startup-inc.com');
        $startupFounder->setPhone('0571-23456789');
        $startupFounder->setMobile('158-2345-6789');
        $startupFounder->setIsPrimary(true);
        $startupFounder->setStatus(ContactStatusEnum::INACTIVE);

        $manager->persist($startupFounder);
        $this->addReference(self::CONTACT_STARTUP_FOUNDER, $startupFounder);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CustomerFixtures::class,
        ];
    }
}
