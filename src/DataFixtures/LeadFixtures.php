<?php

declare(strict_types=1);

namespace JingdongCrmBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use JingdongCrmBundle\Entity\Lead;
use JingdongCrmBundle\Enum\LeadSource;
use JingdongCrmBundle\Enum\LeadStatus;

/**
 * 销售线索测试数据
 */
class LeadFixtures extends Fixture
{
    public const LEAD_ECOMMERCE_PLATFORM = 'jingdong-crm-lead-ecommerce-platform';
    public const LEAD_MOBILE_APP = 'jingdong-crm-lead-mobile-app';
    public const LEAD_ENTERPRISE_SOLUTION = 'jingdong-crm-lead-enterprise-solution';
    public const LEAD_PERSONAL_SERVICE = 'jingdong-crm-lead-personal-service';
    public const LEAD_CLOUD_MIGRATION = 'jingdong-crm-lead-cloud-migration';

    public function load(ObjectManager $manager): void
    {
        // 电商平台开发需求
        $ecommercePlatform = new Lead();
        $ecommercePlatform->setLeadCode('LEAD-2024-001');
        $ecommercePlatform->setCompanyName('深圳贸易有限公司');
        $ecommercePlatform->setContactName('林总监');
        $ecommercePlatform->setTitle('技术总监');
        $ecommercePlatform->setEmail('linzj@sz-trade.com');
        $ecommercePlatform->setPhone('0755-12345678');
        $ecommercePlatform->setSource(LeadSource::WEBSITE);
        $ecommercePlatform->setStatus(LeadStatus::NEW);
        $ecommercePlatform->setScore(85);
        $ecommercePlatform->setNotes('客户需要搭建B2B电商平台，预算100万，预计6个月内启动项目');
        $ecommercePlatform->setAssignedTo('销售经理张三');

        $manager->persist($ecommercePlatform);
        $this->addReference(self::LEAD_ECOMMERCE_PLATFORM, $ecommercePlatform);

        // 移动应用开发
        $mobileApp = new Lead();
        $mobileApp->setLeadCode('LEAD-2024-002');
        $mobileApp->setCompanyName('广州科技创新公司');
        $mobileApp->setContactName('刘产品经理');
        $mobileApp->setTitle('产品经理');
        $mobileApp->setEmail('liupm@gz-tech.com');
        $mobileApp->setPhone('020-87654321');
        $mobileApp->setSource(LeadSource::REFERRAL);
        $mobileApp->setStatus(LeadStatus::IN_PROGRESS);
        $mobileApp->setScore(70);
        $mobileApp->setNotes('需要开发配送管理移动应用，已进行初步沟通');
        $mobileApp->setAssignedTo('销售经理李四');

        $manager->persist($mobileApp);
        $this->addReference(self::LEAD_MOBILE_APP, $mobileApp);

        // 企业数字化解决方案
        $enterpriseSolution = new Lead();
        $enterpriseSolution->setLeadCode('LEAD-2024-003');
        $enterpriseSolution->setCompanyName('武汉制造集团');
        $enterpriseSolution->setContactName('吴副总');
        $enterpriseSolution->setTitle('副总经理');
        $enterpriseSolution->setEmail('wuvp@wh-manufacturing.com');
        $enterpriseSolution->setPhone('027-23456789');
        $enterpriseSolution->setSource(LeadSource::ADVERTISEMENT);
        $enterpriseSolution->setStatus(LeadStatus::CONVERTED);
        $enterpriseSolution->setScore(90);
        $enterpriseSolution->setNotes('大型制造企业，需要完整的数字化转型方案，项目预算500万');
        $enterpriseSolution->setAssignedTo('高级销售顾问王五');

        $manager->persist($enterpriseSolution);
        $this->addReference(self::LEAD_ENTERPRISE_SOLUTION, $enterpriseSolution);

        // 个人客户咨询
        $personalService = new Lead();
        $personalService->setLeadCode('LEAD-2024-004');
        $personalService->setCompanyName('个人工作室');
        $personalService->setContactName('周设计师');
        $personalService->setTitle('自由设计师');
        $personalService->setEmail('zhoudesigner@email.com');
        $personalService->setPhone('185-3333-4444');
        $personalService->setSource(LeadSource::PHONE);
        $personalService->setStatus(LeadStatus::CLOSED);
        $personalService->setScore(25);
        $personalService->setNotes('个人客户，项目规模较小，预算有限');
        $personalService->setAssignedTo('客服专员赵六');

        $manager->persist($personalService);
        $this->addReference(self::LEAD_PERSONAL_SERVICE, $personalService);

        // 云迁移项目
        $cloudMigration = new Lead();
        $cloudMigration->setLeadCode('LEAD-2024-005');
        $cloudMigration->setCompanyName('成都金融服务公司');
        $cloudMigration->setContactName('钱IT总监');
        $cloudMigration->setTitle('IT总监');
        $cloudMigration->setEmail('qianit@cd-finance.com');
        $cloudMigration->setPhone('028-12345678');
        $cloudMigration->setSource(LeadSource::OTHER);
        $cloudMigration->setStatus(LeadStatus::CLOSED);
        $cloudMigration->setScore(60);
        $cloudMigration->setNotes('云迁移项目，最终选择了其他供应商');
        $cloudMigration->setAssignedTo('技术销售孙七');

        $manager->persist($cloudMigration);
        $this->addReference(self::LEAD_CLOUD_MIGRATION, $cloudMigration);

        $manager->flush();
    }
}
