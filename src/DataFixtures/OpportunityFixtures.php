<?php

declare(strict_types=1);

namespace JingdongCrmBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use JingdongCrmBundle\Entity\Customer;
use JingdongCrmBundle\Entity\Opportunity;
use JingdongCrmBundle\Enum\OpportunityStageEnum;
use JingdongCrmBundle\Enum\OpportunityStatusEnum;

/**
 * 销售机会测试数据
 */
class OpportunityFixtures extends Fixture implements DependentFixtureInterface
{
    public const OPPORTUNITY_TECH_CORP_SYSTEM = 'jingdong-crm-opportunity-tech-corp-system';
    public const OPPORTUNITY_DIGITAL_SOLUTIONS_PLATFORM = 'jingdong-crm-opportunity-digital-solutions-platform';
    public const OPPORTUNITY_JOHN_DOE_LAPTOP = 'jingdong-crm-opportunity-john-doe-laptop';
    public const OPPORTUNITY_STARTUP_CONSULTING = 'jingdong-crm-opportunity-startup-consulting';
    public const OPPORTUNITY_JANE_SMITH_PHONE = 'jingdong-crm-opportunity-jane-smith-phone';

    public function load(ObjectManager $manager): void
    {
        // 科技公司 - ERP系统升级项目
        $techCorpSystem = new Opportunity();
        $techCorpSystem->setOpportunityCode('OPP-TECH-001');
        $techCorpSystem->setCustomer($this->getReference(CustomerFixtures::CUSTOMER_TECH_CORP, Customer::class));
        $techCorpSystem->setName('企业ERP系统升级改造项目');
        $techCorpSystem->setDescription('为北京科技有限公司提供全套ERP系统升级改造服务，包括需求分析、系统设计、开发实施和培训支持');
        $techCorpSystem->setStage(OpportunityStageEnum::BUSINESS_NEGOTIATION);
        $techCorpSystem->setAmount('800000.00');
        $techCorpSystem->setProbability(75);
        $techCorpSystem->setExpectedCloseDate(new \DateTimeImmutable('+2 months'));
        $techCorpSystem->setAssignedTo('高级销售经理张经理');
        $techCorpSystem->setSource('客户主动询价');
        $techCorpSystem->setStatus(OpportunityStatusEnum::ACTIVE);

        $manager->persist($techCorpSystem);
        $this->addReference(self::OPPORTUNITY_TECH_CORP_SYSTEM, $techCorpSystem);

        // 数字化解决方案公司 - 数据分析平台
        $digitalSolutionsPlatform = new Opportunity();
        $digitalSolutionsPlatform->setOpportunityCode('OPP-DIGITAL-001');
        $digitalSolutionsPlatform->setCustomer($this->getReference(CustomerFixtures::CUSTOMER_DIGITAL_SOLUTIONS, Customer::class));
        $digitalSolutionsPlatform->setName('大数据分析平台建设');
        $digitalSolutionsPlatform->setDescription('构建企业级大数据分析平台，支持实时数据处理和智能分析');
        $digitalSolutionsPlatform->setStage(OpportunityStageEnum::CONTRACT_SIGNING);
        $digitalSolutionsPlatform->setAmount('1200000.00');
        $digitalSolutionsPlatform->setProbability(90);
        $digitalSolutionsPlatform->setExpectedCloseDate(new \DateTimeImmutable('+1 month'));
        $digitalSolutionsPlatform->setAssignedTo('技术总监李总监');
        $digitalSolutionsPlatform->setSource('合作伙伴推荐');
        $digitalSolutionsPlatform->setStatus(OpportunityStatusEnum::ACTIVE);

        $manager->persist($digitalSolutionsPlatform);
        $this->addReference(self::OPPORTUNITY_DIGITAL_SOLUTIONS_PLATFORM, $digitalSolutionsPlatform);

        // 个人客户 - 笔记本采购
        $johnDoeLaptop = new Opportunity();
        $johnDoeLaptop->setOpportunityCode('OPP-IND-001');
        $johnDoeLaptop->setCustomer($this->getReference(CustomerFixtures::CUSTOMER_JOHN_DOE, Customer::class));
        $johnDoeLaptop->setName('高端商务笔记本采购');
        $johnDoeLaptop->setDescription('采购5台ThinkPad X1 Carbon笔记本用于团队办公');
        $johnDoeLaptop->setStage(OpportunityStageEnum::SOLUTION_DESIGN);
        $johnDoeLaptop->setAmount('49995.00');
        $johnDoeLaptop->setProbability(60);
        $johnDoeLaptop->setExpectedCloseDate(new \DateTimeImmutable('+3 weeks'));
        $johnDoeLaptop->setAssignedTo('销售代表王代表');
        $johnDoeLaptop->setSource('线上查询');
        $johnDoeLaptop->setStatus(OpportunityStatusEnum::ACTIVE);

        $manager->persist($johnDoeLaptop);
        $this->addReference(self::OPPORTUNITY_JOHN_DOE_LAPTOP, $johnDoeLaptop);

        // 创业公司 - 技术咨询服务
        $startupConsulting = new Opportunity();
        $startupConsulting->setOpportunityCode('OPP-STARTUP-001');
        $startupConsulting->setCustomer($this->getReference(CustomerFixtures::CUSTOMER_STARTUP_INC, Customer::class));
        $startupConsulting->setName('技术架构咨询服务');
        $startupConsulting->setDescription('为创业公司提供技术架构设计和系统规划咨询服务');
        $startupConsulting->setStage(OpportunityStageEnum::IDENTIFY_NEEDS);
        $startupConsulting->setAmount('180000.00');
        $startupConsulting->setProbability(30);
        $startupConsulting->setExpectedCloseDate(new \DateTimeImmutable('+6 weeks'));
        $startupConsulting->setAssignedTo('技术顾问赵顾问');
        $startupConsulting->setSource('会展推广');
        $startupConsulting->setStatus(OpportunityStatusEnum::LOST);

        $manager->persist($startupConsulting);
        $this->addReference(self::OPPORTUNITY_STARTUP_CONSULTING, $startupConsulting);

        // 个人客户 - 手机采购
        $janeSmithPhone = new Opportunity();
        $janeSmithPhone->setOpportunityCode('OPP-IND-002');
        $janeSmithPhone->setCustomer($this->getReference(CustomerFixtures::CUSTOMER_JANE_SMITH, Customer::class));
        $janeSmithPhone->setName('iPhone 14 Pro 采购');
        $janeSmithPhone->setDescription('采购2台iPhone 14 Pro，256GB版本');
        $janeSmithPhone->setStage(OpportunityStageEnum::CLOSED_WON);
        $janeSmithPhone->setAmount('15998.00');
        $janeSmithPhone->setProbability(100);
        $janeSmithPhone->setExpectedCloseDate(new \DateTimeImmutable('-1 week'));
        $janeSmithPhone->setAssignedTo('销售专员孙专员');
        $janeSmithPhone->setSource('朋友推荐');
        $janeSmithPhone->setStatus(OpportunityStatusEnum::WON);

        $manager->persist($janeSmithPhone);
        $this->addReference(self::OPPORTUNITY_JANE_SMITH_PHONE, $janeSmithPhone);

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            CustomerFixtures::class,
        ];
    }
}
