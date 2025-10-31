<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Tests\Repository;

use JingdongCrmBundle\Entity\Customer;
use JingdongCrmBundle\Entity\Opportunity;
use JingdongCrmBundle\Enum\CustomerStatusEnum;
use JingdongCrmBundle\Enum\CustomerTypeEnum;
use JingdongCrmBundle\Enum\OpportunityStageEnum;
use JingdongCrmBundle\Enum\OpportunityStatusEnum;
use JingdongCrmBundle\Repository\OpportunityRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * OpportunityRepository测试类
 * @internal
 */
#[CoversClass(OpportunityRepository::class)]
#[RunTestsInSeparateProcesses]
final class OpportunityRepositoryTest extends AbstractRepositoryTestCase
{
    protected function createNewEntity(): object
    {
        $customer = $this->createTestCustomer();

        $opportunity = new Opportunity();
        $opportunity->setOpportunityCode('OPP-' . uniqid());
        $opportunity->setName('测试商机');
        $opportunity->setCustomer($customer);
        $opportunity->setStatus(OpportunityStatusEnum::ACTIVE);
        $opportunity->setStage(OpportunityStageEnum::IDENTIFY_NEEDS);
        $opportunity->setAmount('10000.00');

        return $opportunity;
    }

    protected function getRepository(): OpportunityRepository
    {
        return self::getService(OpportunityRepository::class);
    }

    protected function onSetUp(): void
    {
        // 测试设置完成后的操作
    }

    private function createTestCustomer(string $suffix = ''): Customer
    {
        $customer = new Customer();
        $uniqueId = uniqid('', true);
        $customer->setName('测试客户' . $suffix);
        $customer->setCustomerCode('CUST-' . $uniqueId . $suffix);
        $customer->setJdCustomerId('JD-' . $uniqueId . $suffix);
        $customer->setStatus(CustomerStatusEnum::ACTIVE);
        $customer->setType(CustomerTypeEnum::ENTERPRISE);

        $entityManager = self::getEntityManager();
        $entityManager->persist($customer);
        $entityManager->flush();

        return $customer;
    }

    public function testFindByOpportunityCode(): void
    {
        $repository = $this->getRepository();
        $customer = $this->createTestCustomer();

        // 创建测试商机
        $opportunity = new Opportunity();
        $opportunityCode = 'TEST-OPP-' . uniqid();
        $opportunity->setOpportunityCode($opportunityCode);
        $opportunity->setName('测试商机编码查找');
        $opportunity->setCustomer($customer);
        $opportunity->setStatus(OpportunityStatusEnum::ACTIVE);
        $opportunity->setStage(OpportunityStageEnum::IDENTIFY_NEEDS);
        $opportunity->setAmount('15000.00');

        $repository->save($opportunity, true);

        // 测试正常查找
        $foundOpportunity = $repository->findByOpportunityCode($opportunityCode);
        $this->assertNotNull($foundOpportunity);
        $this->assertEquals($opportunityCode, $foundOpportunity->getOpportunityCode());
        $this->assertEquals('测试商机编码查找', $foundOpportunity->getName());

        // 测试查找不存在的商机编码
        $notFoundOpportunity = $repository->findByOpportunityCode('NON-EXISTENT-OPP');
        $this->assertNull($notFoundOpportunity);
    }

    public function testFindActiveOpportunities(): void
    {
        $repository = $this->getRepository();
        $customer = $this->createTestCustomer();

        // 创建活跃商机
        $activeOpportunity1 = new Opportunity();
        $activeOpportunity1->setOpportunityCode('ACTIVE-OPP-1-' . uniqid());
        $activeOpportunity1->setName('活跃商机A');
        $activeOpportunity1->setCustomer($customer);
        $activeOpportunity1->setStatus(OpportunityStatusEnum::ACTIVE);
        $activeOpportunity1->setStage(OpportunityStageEnum::IDENTIFY_NEEDS);
        $activeOpportunity1->setAmount('20000.00');
        $activeOpportunity1->setExpectedCloseDate(new \DateTimeImmutable('+30 days'));

        $activeOpportunity2 = new Opportunity();
        $activeOpportunity2->setOpportunityCode('ACTIVE-OPP-2-' . uniqid());
        $activeOpportunity2->setName('活跃商机B');
        $activeOpportunity2->setCustomer($customer);
        $activeOpportunity2->setStatus(OpportunityStatusEnum::ACTIVE);
        $activeOpportunity2->setStage(OpportunityStageEnum::BUSINESS_NEGOTIATION);
        $activeOpportunity2->setAmount('25000.00');
        $activeOpportunity2->setExpectedCloseDate(new \DateTimeImmutable('+15 days'));

        // 创建非活跃商机
        $closedOpportunity = new Opportunity();
        $closedOpportunity->setOpportunityCode('CLOSED-OPP-' . uniqid());
        $closedOpportunity->setName('已关闭商机');
        $closedOpportunity->setCustomer($customer);
        $closedOpportunity->setStatus(OpportunityStatusEnum::ACTIVE);
        $closedOpportunity->setStage(OpportunityStageEnum::CLOSED_WON);
        $closedOpportunity->setAmount('30000.00');
        $closedOpportunity->setExpectedCloseDate(new \DateTimeImmutable('+45 days'));

        $repository->save($activeOpportunity1, true);
        $repository->save($activeOpportunity2, true);
        $repository->save($closedOpportunity, true);

        // 测试查找活跃商机（过滤我们的测试数据）
        $allActiveOpportunities = $repository->findActiveOpportunities();
        $activeOpportunities = array_filter($allActiveOpportunities, function ($opp) {
            return str_contains($opp->getOpportunityCode(), 'ACTIVE-OPP-');
        });
        $activeOpportunities = array_values($activeOpportunities); // 重新索引数组
        $this->assertCount(2, $activeOpportunities);

        // 验证按预期关闭日期排序（最近的在前）
        $this->assertEquals('活跃商机B', $activeOpportunities[0]->getName());
        $this->assertEquals('活跃商机A', $activeOpportunities[1]->getName());

        // 验证所有商机都是活跃状态
        foreach ($activeOpportunities as $opportunity) {
            $this->assertEquals(OpportunityStatusEnum::ACTIVE, $opportunity->getStatus());
        }
    }

    public function testFindByCustomer(): void
    {
        $repository = $this->getRepository();
        $customer1 = $this->createTestCustomer('1');
        $customer2 = $this->createTestCustomer('2');

        // 为customer1创建商机
        $opportunity1 = new Opportunity();
        $opportunity1->setOpportunityCode('CUST1-OPP-1-' . uniqid());
        $opportunity1->setName('客户1商机A');
        $opportunity1->setCustomer($customer1);
        $opportunity1->setStatus(OpportunityStatusEnum::ACTIVE);
        $opportunity1->setStage(OpportunityStageEnum::IDENTIFY_NEEDS);
        $opportunity1->setAmount('10000.00');

        $opportunity2 = new Opportunity();
        $opportunity2->setOpportunityCode('CUST1-OPP-2-' . uniqid());
        $opportunity2->setName('客户1商机B');
        $opportunity2->setCustomer($customer1);
        $opportunity2->setStatus(OpportunityStatusEnum::ACTIVE);
        $opportunity2->setStage(OpportunityStageEnum::SOLUTION_DESIGN);
        $opportunity2->setAmount('15000.00');

        // 为customer2创建商机
        $opportunity3 = new Opportunity();
        $opportunity3->setOpportunityCode('CUST2-OPP-1-' . uniqid());
        $opportunity3->setName('客户2商机');
        $opportunity3->setCustomer($customer2);
        $opportunity3->setStatus(OpportunityStatusEnum::ACTIVE);
        $opportunity3->setStage(OpportunityStageEnum::BUSINESS_NEGOTIATION);
        $opportunity3->setAmount('20000.00');

        $repository->save($opportunity1, true);
        $repository->save($opportunity2, true);
        $repository->save($opportunity3, true);

        // 测试查找customer1的商机
        $customer1Opportunities = $repository->findByCustomer($customer1);
        $this->assertCount(2, $customer1Opportunities);

        // 验证所有商机都属于customer1
        foreach ($customer1Opportunities as $opportunity) {
            $this->assertEquals($customer1->getId(), $opportunity->getCustomer()->getId());
        }

        // 测试查找customer2的商机
        $customer2Opportunities = $repository->findByCustomer($customer2);
        $this->assertCount(1, $customer2Opportunities);
        $this->assertEquals('客户2商机', $customer2Opportunities[0]->getName());
    }

    public function testFindByStage(): void
    {
        $repository = $this->getRepository();
        $customer = $this->createTestCustomer();

        // 创建不同阶段的商机
        $prospectingOpp = new Opportunity();
        $prospectingOpp->setOpportunityCode('PROSPECT-OPP-' . uniqid());
        $prospectingOpp->setName('开发阶段商机');
        $prospectingOpp->setCustomer($customer);
        $prospectingOpp->setStatus(OpportunityStatusEnum::ACTIVE);
        $prospectingOpp->setStage(OpportunityStageEnum::IDENTIFY_NEEDS);
        $prospectingOpp->setAmount('12000.00');
        $prospectingOpp->setExpectedCloseDate(new \DateTimeImmutable('+20 days'));

        $proposalOpp1 = new Opportunity();
        $proposalOpp1->setOpportunityCode('PROPOSAL-OPP-1-' . uniqid());
        $proposalOpp1->setName('提案阶段商机A');
        $proposalOpp1->setCustomer($customer);
        $proposalOpp1->setStatus(OpportunityStatusEnum::ACTIVE);
        $proposalOpp1->setStage(OpportunityStageEnum::BUSINESS_NEGOTIATION);
        $proposalOpp1->setAmount('18000.00');
        $proposalOpp1->setExpectedCloseDate(new \DateTimeImmutable('+10 days'));

        $proposalOpp2 = new Opportunity();
        $proposalOpp2->setOpportunityCode('PROPOSAL-OPP-2-' . uniqid());
        $proposalOpp2->setName('提案阶段商机B');
        $proposalOpp2->setCustomer($customer);
        $proposalOpp2->setStatus(OpportunityStatusEnum::ACTIVE);
        $proposalOpp2->setStage(OpportunityStageEnum::BUSINESS_NEGOTIATION);
        $proposalOpp2->setAmount('22000.00');
        $proposalOpp2->setExpectedCloseDate(new \DateTimeImmutable('+25 days'));

        $repository->save($prospectingOpp, true);
        $repository->save($proposalOpp1, true);
        $repository->save($proposalOpp2, true);

        // 测试查找开发阶段商机（过滤我们的测试数据）
        $allProspectingOpportunities = $repository->findByStage(OpportunityStageEnum::IDENTIFY_NEEDS);
        $prospectingOpportunities = array_filter($allProspectingOpportunities, function ($opp) {
            return str_contains($opp->getOpportunityCode(), 'PROSPECT-OPP-');
        });
        $prospectingOpportunities = array_values($prospectingOpportunities);
        $this->assertCount(1, $prospectingOpportunities);
        $this->assertEquals('开发阶段商机', $prospectingOpportunities[0]->getName());
        $this->assertEquals(OpportunityStageEnum::IDENTIFY_NEEDS, $prospectingOpportunities[0]->getStage());

        // 测试查找提案阶段商机（过滤我们的测试数据）
        $allProposalOpportunities = $repository->findByStage(OpportunityStageEnum::BUSINESS_NEGOTIATION);
        $proposalOpportunities = array_filter($allProposalOpportunities, function ($opp) {
            return str_contains($opp->getOpportunityCode(), 'PROPOSAL-OPP-');
        });
        $proposalOpportunities = array_values($proposalOpportunities);
        $this->assertCount(2, $proposalOpportunities);

        // 验证按预期关闭日期排序
        $this->assertEquals('提案阶段商机A', $proposalOpportunities[0]->getName());
        $this->assertEquals('提案阶段商机B', $proposalOpportunities[1]->getName());

        // 验证所有商机都是提案阶段
        foreach ($proposalOpportunities as $opportunity) {
            $this->assertEquals(OpportunityStageEnum::BUSINESS_NEGOTIATION, $opportunity->getStage());
        }
    }

    public function testFindByAssignedTo(): void
    {
        $repository = $this->getRepository();
        $customer = $this->createTestCustomer();

        // 创建分配给不同销售人员的商机
        $opportunity1 = new Opportunity();
        $opportunity1->setOpportunityCode('SALES-A-OPP-1-' . uniqid());
        $opportunity1->setName('销售A的商机1');
        $opportunity1->setCustomer($customer);
        $opportunity1->setStatus(OpportunityStatusEnum::ACTIVE);
        $opportunity1->setStage(OpportunityStageEnum::IDENTIFY_NEEDS);
        $opportunity1->setAmount('15000.00');
        $opportunity1->setAssignedTo('销售员甲');
        $opportunity1->setExpectedCloseDate(new \DateTimeImmutable('+30 days'));

        $opportunity2 = new Opportunity();
        $opportunity2->setOpportunityCode('SALES-A-OPP-2-' . uniqid());
        $opportunity2->setName('销售A的商机2');
        $opportunity2->setCustomer($customer);
        $opportunity2->setStatus(OpportunityStatusEnum::ACTIVE);
        $opportunity2->setStage(OpportunityStageEnum::SOLUTION_DESIGN);
        $opportunity2->setAmount('20000.00');
        $opportunity2->setAssignedTo('销售员甲');
        $opportunity2->setExpectedCloseDate(new \DateTimeImmutable('+15 days'));

        $opportunity3 = new Opportunity();
        $opportunity3->setOpportunityCode('SALES-B-OPP-1-' . uniqid());
        $opportunity3->setName('销售B的商机');
        $opportunity3->setCustomer($customer);
        $opportunity3->setStatus(OpportunityStatusEnum::ACTIVE);
        $opportunity3->setStage(OpportunityStageEnum::BUSINESS_NEGOTIATION);
        $opportunity3->setAmount('25000.00');
        $opportunity3->setAssignedTo('销售员乙');
        $opportunity3->setExpectedCloseDate(new \DateTimeImmutable('+45 days'));

        $repository->save($opportunity1, true);
        $repository->save($opportunity2, true);
        $repository->save($opportunity3, true);

        // 测试查找分配给销售员甲的商机
        $salesAOpportunities = $repository->findByAssignedTo('销售员甲');
        $this->assertCount(2, $salesAOpportunities);

        // 验证按预期关闭日期排序
        $this->assertEquals('销售A的商机2', $salesAOpportunities[0]->getName());
        $this->assertEquals('销售A的商机1', $salesAOpportunities[1]->getName());

        // 验证所有商机都分配给销售员甲
        foreach ($salesAOpportunities as $opportunity) {
            $this->assertEquals('销售员甲', $opportunity->getAssignedTo());
        }

        // 测试查找分配给销售员乙的商机
        $salesBOpportunities = $repository->findByAssignedTo('销售员乙');
        $this->assertCount(1, $salesBOpportunities);
        $this->assertEquals('销售B的商机', $salesBOpportunities[0]->getName());
        $this->assertEquals('销售员乙', $salesBOpportunities[0]->getAssignedTo());
    }

    public function testOpportunitySaveAndRemove(): void
    {
        $repository = $this->getRepository();
        $customer = $this->createTestCustomer();

        // 测试save方法
        $opportunity = new Opportunity();
        $opportunityCode = 'SAVE-TEST-' . uniqid();
        $opportunity->setOpportunityCode($opportunityCode);
        $opportunity->setName('保存测试商机');
        $opportunity->setCustomer($customer);
        $opportunity->setStatus(OpportunityStatusEnum::ACTIVE);
        $opportunity->setStage(OpportunityStageEnum::IDENTIFY_NEEDS);
        $opportunity->setAmount('8000.00');

        // 测试不flush的save
        $repository->save($opportunity, false);
        $foundBeforeFlush = $repository->findByOpportunityCode($opportunityCode);
        $this->assertNull($foundBeforeFlush); // 还未flush，不应该找到

        // 手动flush
        $entityManager = self::getEntityManager();
        $entityManager->flush();
        $foundAfterFlush = $repository->findByOpportunityCode($opportunityCode);
        $this->assertNotNull($foundAfterFlush);
        $this->assertEquals($opportunityCode, $foundAfterFlush->getOpportunityCode());

        // 测试带flush的save
        $opportunity2 = new Opportunity();
        $opportunityCode2 = 'SAVE-TEST-2-' . uniqid();
        $opportunity2->setOpportunityCode($opportunityCode2);
        $opportunity2->setName('保存测试商机2');
        $opportunity2->setCustomer($customer);
        $opportunity2->setStatus(OpportunityStatusEnum::ACTIVE);
        $opportunity2->setStage(OpportunityStageEnum::SOLUTION_DESIGN);
        $opportunity2->setAmount('12000.00');

        $repository->save($opportunity2, true);
        $foundImmediately = $repository->findByOpportunityCode($opportunityCode2);
        $this->assertNotNull($foundImmediately);
        $this->assertEquals($opportunityCode2, $foundImmediately->getOpportunityCode());

        // 测试remove方法
        $repository->remove($foundImmediately, true);
        $removedOpportunity = $repository->findByOpportunityCode($opportunityCode2);
        $this->assertNull($removedOpportunity);
    }

    public function testOpportunitySaveWithoutFlush(): void
    {
        $repository = $this->getRepository();
        $customer = $this->createTestCustomer();

        $opportunity = new Opportunity();
        $opportunityCode = 'NO-FLUSH-TEST-' . uniqid();
        $opportunity->setOpportunityCode($opportunityCode);
        $opportunity->setName('无刷新测试商机');
        $opportunity->setCustomer($customer);
        $opportunity->setStatus(OpportunityStatusEnum::ACTIVE);
        $opportunity->setStage(OpportunityStageEnum::IDENTIFY_NEEDS);
        $opportunity->setAmount('9000.00');

        // 不使用flush的save
        $repository->save($opportunity, false);

        // persist后实体被管理，但还未flush到数据库
        // 使用查询方法无法找到（因为还没有写入数据库）
        $foundBeforeFlush = $repository->findByOpportunityCode($opportunityCode);
        $this->assertNull($foundBeforeFlush); // 还未flush，查询找不到

        // flush后应该能找到
        $entityManager = self::getEntityManager();
        $entityManager->flush();
        $foundOpportunity = $repository->findByOpportunityCode($opportunityCode);
        $this->assertNotNull($foundOpportunity);
        $this->assertEquals('无刷新测试商机', $foundOpportunity->getName());
    }

    public function testOpportunityRemoveWithoutFlush(): void
    {
        $repository = $this->getRepository();
        $customer = $this->createTestCustomer();

        // 先创建一个商机
        $opportunity = new Opportunity();
        $opportunityCode = 'REMOVE-TEST-' . uniqid();
        $opportunity->setOpportunityCode($opportunityCode);
        $opportunity->setName('删除测试商机');
        $opportunity->setCustomer($customer);
        $opportunity->setStatus(OpportunityStatusEnum::ACTIVE);
        $opportunity->setStage(OpportunityStageEnum::IDENTIFY_NEEDS);
        $opportunity->setAmount('7000.00');

        $repository->save($opportunity, true);
        $this->assertNotNull($repository->findByOpportunityCode($opportunityCode));

        // 不使用flush的remove
        $repository->remove($opportunity, false);

        // 在同一事务中仍然可以找到
        $stillExists = $repository->findByOpportunityCode($opportunityCode);
        $this->assertNotNull($stillExists);

        // flush后应该被删除
        $entityManager = self::getEntityManager();
        $entityManager->flush();
        $removedOpportunity = $repository->findByOpportunityCode($opportunityCode);
        $this->assertNull($removedOpportunity);
    }
}
