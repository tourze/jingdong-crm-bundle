<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Tests\Repository;

use JingdongCrmBundle\Entity\Lead;
use JingdongCrmBundle\Enum\LeadSource;
use JingdongCrmBundle\Enum\LeadStatus;
use JingdongCrmBundle\Repository\LeadRepository;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractRepositoryTestCase;

/**
 * LeadRepository测试类
 * @internal
 */
#[CoversClass(LeadRepository::class)]
#[RunTestsInSeparateProcesses]
final class LeadRepositoryTest extends AbstractRepositoryTestCase
{
    protected function createNewEntity(): object
    {
        $lead = new Lead();
        $lead->setLeadCode('LEAD-' . uniqid());
        $lead->setCompanyName('测试公司');
        $lead->setContactName('测试联系人');
        $lead->setSource(LeadSource::WEBSITE);
        $lead->setSource(LeadSource::WEBSITE);
        $lead->setStatus(LeadStatus::NEW);
        $lead->setScore(50);

        return $lead;
    }

    protected function getRepository(): LeadRepository
    {
        return self::getService(LeadRepository::class);
    }

    protected function onSetUp(): void
    {
        // 测试设置完成后的操作
    }

    public function testFindOneByLeadCode(): void
    {
        $repository = $this->getRepository();

        // 创建测试线索
        $lead = new Lead();
        $leadCode = 'TEST-LEAD-' . uniqid();
        $lead->setLeadCode($leadCode);
        $lead->setCompanyName('测试线索编码查找公司');
        $lead->setContactName('测试联系人');
        $lead->setSource(LeadSource::WEBSITE);
        $lead->setStatus(LeadStatus::NEW);
        $lead->setScore(75);

        $repository->save($lead, true);

        // 测试正常查找
        $foundLead = $repository->findOneByLeadCode($leadCode);
        $this->assertNotNull($foundLead);
        $this->assertEquals($leadCode, $foundLead->getLeadCode());
        $this->assertEquals('测试线索编码查找公司', $foundLead->getCompanyName());

        // 测试查找不存在的线索编码
        $notFoundLead = $repository->findOneByLeadCode('NON-EXISTENT-LEAD');
        $this->assertNull($notFoundLead);
    }

    public function testFindByStatus(): void
    {
        $repository = $this->getRepository();

        // 创建不同状态的线索
        $newLead1 = new Lead();
        $newLead1->setLeadCode('NEW-LEAD-1-' . uniqid());
        $newLead1->setCompanyName('新线索公司A');
        $newLead1->setContactName('联系人A');
        $newLead1->setSource(LeadSource::WEBSITE);
        $newLead1->setStatus(LeadStatus::NEW);
        $newLead1->setScore(60);

        $newLead2 = new Lead();
        $newLead2->setLeadCode('NEW-LEAD-2-' . uniqid());
        $newLead2->setCompanyName('新线索公司B');
        $newLead2->setContactName('联系人B');
        $newLead2->setSource(LeadSource::PHONE);
        $newLead2->setStatus(LeadStatus::NEW);
        $newLead2->setScore(80);

        $qualifiedLead = new Lead();
        $qualifiedLead->setLeadCode('QUALIFIED-LEAD-' . uniqid());
        $qualifiedLead->setCompanyName('转换线索公司');
        $qualifiedLead->setContactName('合格联系人');
        $qualifiedLead->setSource(LeadSource::OTHER);
        $qualifiedLead->setStatus(LeadStatus::CONVERTED);
        $qualifiedLead->setScore(90);

        $repository->save($newLead1, true);
        $repository->save($newLead2, true);
        $repository->save($qualifiedLead, true);

        // 测试查找新线索（过滤我们的测试数据）
        $allNewLeads = $repository->findByStatus(LeadStatus::NEW);
        $newLeads = array_filter($allNewLeads, function ($lead) {
            return str_contains($lead->getCompanyName(), '新线索公司');
        });
        $this->assertCount(2, $newLeads);

        // 验证所有线索都是新状态
        foreach ($newLeads as $lead) {
            $this->assertEquals(LeadStatus::NEW, $lead->getStatus());
        }

        // 测试查找转换线索（过滤我们的测试数据）
        $allQualifiedLeads = $repository->findByStatus(LeadStatus::CONVERTED);
        $qualifiedLeads = array_filter($allQualifiedLeads, function ($lead) {
            return str_contains($lead->getCompanyName(), '转换线索公司');
        });
        $this->assertCount(1, $qualifiedLeads);
        $qualifiedLead = reset($qualifiedLeads);
        $this->assertEquals('转换线索公司', $qualifiedLead->getCompanyName());
        $this->assertEquals(LeadStatus::CONVERTED, $qualifiedLead->getStatus());
    }

    public function testFindByAssignedTo(): void
    {
        $repository = $this->getRepository();

        // 创建分配给不同人员的线索
        $lead1 = new Lead();
        $lead1->setLeadCode('ASSIGNED-1-' . uniqid());
        $lead1->setCompanyName('分配测试公司A');
        $lead1->setContactName('联系人A');
        $lead1->setSource(LeadSource::REFERRAL);
        $lead1->setStatus(LeadStatus::IN_PROGRESS);
        $lead1->setAssignedTo('销售员甲');
        $lead1->setScore(70);

        $lead2 = new Lead();
        $lead2->setLeadCode('ASSIGNED-2-' . uniqid());
        $lead2->setCompanyName('分配测试公司B');
        $lead2->setContactName('联系人B');
        $lead2->setSource(LeadSource::WEBSITE);
        $lead2->setStatus(LeadStatus::NEW);
        $lead2->setAssignedTo('销售员甲');
        $lead2->setScore(65);

        $lead3 = new Lead();
        $lead3->setLeadCode('ASSIGNED-3-' . uniqid());
        $lead3->setCompanyName('分配测试公司C');
        $lead3->setContactName('联系人C');
        $lead3->setSource(LeadSource::PHONE);
        $lead3->setStatus(LeadStatus::CONVERTED);
        $lead3->setAssignedTo('销售员乙');
        $lead3->setScore(85);

        $repository->save($lead1, true);
        $repository->save($lead2, true);
        $repository->save($lead3, true);

        // 测试查找分配给销售员甲的线索
        $leadsForSalesA = $repository->findByAssignedTo('销售员甲');
        $this->assertCount(2, $leadsForSalesA);

        // 验证所有线索都分配给销售员甲
        foreach ($leadsForSalesA as $lead) {
            $this->assertEquals('销售员甲', $lead->getAssignedTo());
        }

        // 测试查找分配给销售员乙的线索
        $leadsForSalesB = $repository->findByAssignedTo('销售员乙');
        $this->assertCount(1, $leadsForSalesB);
        $this->assertEquals('分配测试公司C', $leadsForSalesB[0]->getCompanyName());
        $this->assertEquals('销售员乙', $leadsForSalesB[0]->getAssignedTo());
    }

    public function testFindByCompanyName(): void
    {
        $repository = $this->getRepository();

        // 创建不同公司名称的线索
        $lead1 = new Lead();
        $lead1->setLeadCode('COMPANY-1-' . uniqid());
        $lead1->setCompanyName('北京科技有限公司');
        $lead1->setContactName('联系人1');
        $lead1->setSource(LeadSource::WEBSITE);
        $lead1->setStatus(LeadStatus::NEW);
        $lead1->setScore(60);

        $lead2 = new Lead();
        $lead2->setLeadCode('COMPANY-2-' . uniqid());
        $lead2->setCompanyName('上海科技股份公司');
        $lead2->setContactName('联系人2');
        $lead2->setSource(LeadSource::ADVERTISEMENT);
        $lead2->setStatus(LeadStatus::IN_PROGRESS);
        $lead2->setScore(75);

        $lead3 = new Lead();
        $lead3->setLeadCode('COMPANY-3-' . uniqid());
        $lead3->setCompanyName('广州贸易公司');
        $lead3->setContactName('联系人3');
        $lead3->setSource(LeadSource::REFERRAL);
        $lead3->setStatus(LeadStatus::IN_PROGRESS);
        $lead3->setScore(80);

        $repository->save($lead1, true);
        $repository->save($lead2, true);
        $repository->save($lead3, true);

        // 测试模糊查找包含"科技"的公司（过滤我们的测试数据）
        $allTechCompanies = $repository->findByCompanyName('科技');
        $techCompanies = array_filter($allTechCompanies, function ($lead) {
            return str_contains($lead->getLeadCode(), 'COMPANY-');
        });
        $this->assertCount(2, $techCompanies);

        // 验证结果包含正确的公司
        $companyNames = array_map(fn ($lead) => $lead->getCompanyName(), $techCompanies);
        $this->assertContains('北京科技有限公司', $companyNames);
        $this->assertContains('上海科技股份公司', $companyNames);

        // 测试查找包含"贸易"的公司（过滤我们的测试数据）
        $allTradeCompanies = $repository->findByCompanyName('贸易');
        $tradeCompanies = array_filter($allTradeCompanies, function ($lead) {
            return str_contains($lead->getLeadCode(), 'COMPANY-');
        });
        $this->assertCount(1, $tradeCompanies);
        $tradeCompany = reset($tradeCompanies);
        $this->assertEquals('广州贸易公司', $tradeCompany->getCompanyName());

        // 测试查找不存在的公司名称
        $nonExistentCompanies = $repository->findByCompanyName('不存在的公司');
        $this->assertEmpty($nonExistentCompanies);
    }

    public function testFindByScoreRange(): void
    {
        $repository = $this->getRepository();

        // 创建不同评分的线索
        $lowScoreLead = new Lead();
        $lowScoreLead->setLeadCode('LOW-SCORE-' . uniqid());
        $lowScoreLead->setCompanyName('低分公司');
        $lowScoreLead->setContactName('低分联系人');
        $lowScoreLead->setSource(LeadSource::WEBSITE);
        $lowScoreLead->setStatus(LeadStatus::NEW);
        $lowScoreLead->setScore(30);

        $mediumScoreLead = new Lead();
        $mediumScoreLead->setLeadCode('MEDIUM-SCORE-' . uniqid());
        $mediumScoreLead->setCompanyName('中等分数公司');
        $mediumScoreLead->setContactName('中等分数联系人');
        $mediumScoreLead->setSource(LeadSource::PHONE);
        $mediumScoreLead->setStatus(LeadStatus::CONVERTED);
        $mediumScoreLead->setScore(65);

        $highScoreLead = new Lead();
        $highScoreLead->setLeadCode('HIGH-SCORE-' . uniqid());
        $highScoreLead->setCompanyName('高分公司');
        $highScoreLead->setContactName('高分联系人');
        $highScoreLead->setSource(LeadSource::REFERRAL);
        $highScoreLead->setStatus(LeadStatus::CONVERTED);
        $highScoreLead->setScore(90);

        $repository->save($lowScoreLead, true);
        $repository->save($mediumScoreLead, true);
        $repository->save($highScoreLead, true);

        // 测试查找评分在60-80范围的线索（过滤我们的测试数据）
        $allMidRangeLeads = $repository->findByScoreRange(60, 80);
        $midRangeLeads = array_filter($allMidRangeLeads, function ($lead) {
            return str_contains($lead->getLeadCode(), 'SCORE-');
        });
        $this->assertCount(1, $midRangeLeads);
        $midRangeLead = reset($midRangeLeads);
        $this->assertEquals('中等分数公司', $midRangeLead->getCompanyName());
        $this->assertEquals(65, $midRangeLead->getScore());

        // 测试查找评分大于等于80的线索（过滤我们的测试数据）
        $allHighScoreLeads = $repository->findByScoreRange(80, null);
        $highScoreLeads = array_filter($allHighScoreLeads, function ($lead) {
            return str_contains($lead->getLeadCode(), 'SCORE-');
        });
        $this->assertCount(1, $highScoreLeads);
        $highScoreLead = reset($highScoreLeads);
        $this->assertEquals('高分公司', $highScoreLead->getCompanyName());
        $this->assertEquals(90, $highScoreLead->getScore());

        // 测试查找评分小于等于50的线索（过滤我们的测试数据）
        $allLowScoreLeads = $repository->findByScoreRange(null, 50);
        $lowScoreLeads = array_filter($allLowScoreLeads, function ($lead) {
            return str_contains($lead->getLeadCode(), 'SCORE-');
        });
        $this->assertCount(1, $lowScoreLeads);
        $lowScoreLead = reset($lowScoreLeads);
        $this->assertEquals('低分公司', $lowScoreLead->getCompanyName());
        $this->assertEquals(30, $lowScoreLead->getScore());

        // 测试查找所有线索（无分数限制，过滤我们的测试数据）
        $allLeadsInDb = $repository->findByScoreRange(null, null);
        $allLeads = array_filter($allLeadsInDb, function ($lead) {
            return str_contains($lead->getLeadCode(), 'SCORE-');
        });
        $allLeads = array_values($allLeads); // 重新索引数组
        $this->assertCount(3, $allLeads);

        // 验证结果按评分降序排序
        $this->assertEquals(90, $allLeads[0]->getScore());
        $this->assertEquals(65, $allLeads[1]->getScore());
        $this->assertEquals(30, $allLeads[2]->getScore());
    }

    public function testFindHighScoreLeads(): void
    {
        $repository = $this->getRepository();

        // 创建不同评分的线索
        $lowScoreLead = new Lead();
        $lowScoreLead->setLeadCode('LOW-' . uniqid());
        $lowScoreLead->setCompanyName('低分公司');
        $lowScoreLead->setContactName('低分联系人');
        $lowScoreLead->setSource(LeadSource::WEBSITE);
        $lowScoreLead->setStatus(LeadStatus::NEW);
        $lowScoreLead->setScore(75); // 低于80分

        $highScoreLead1 = new Lead();
        $highScoreLead1->setLeadCode('HIGH-1-' . uniqid());
        $highScoreLead1->setCompanyName('高分公司A');
        $highScoreLead1->setContactName('高分联系人A');
        $highScoreLead1->setSource(LeadSource::ADVERTISEMENT);
        $highScoreLead1->setStatus(LeadStatus::CONVERTED);
        $highScoreLead1->setScore(85);

        $highScoreLead2 = new Lead();
        $highScoreLead2->setLeadCode('HIGH-2-' . uniqid());
        $highScoreLead2->setCompanyName('高分公司B');
        $highScoreLead2->setContactName('高分联系人B');
        $highScoreLead2->setSource(LeadSource::OTHER);
        $highScoreLead2->setStatus(LeadStatus::CONVERTED);
        $highScoreLead2->setScore(95);

        $exactScoreLead = new Lead();
        $exactScoreLead->setLeadCode('EXACT-' . uniqid());
        $exactScoreLead->setCompanyName('精确分数公司');
        $exactScoreLead->setContactName('精确分数联系人');
        $exactScoreLead->setSource(LeadSource::WEBSITE);
        $exactScoreLead->setStatus(LeadStatus::IN_PROGRESS);
        $exactScoreLead->setScore(80); // 正好80分

        $repository->save($lowScoreLead, true);
        $repository->save($highScoreLead1, true);
        $repository->save($highScoreLead2, true);
        $repository->save($exactScoreLead, true);

        // 测试查找高分线索（评分>=80，过滤我们的测试数据）
        $allHighScoreLeads = $repository->findHighScoreLeads();
        $highScoreLeads = array_filter($allHighScoreLeads, function ($lead) {
            $code = $lead->getLeadCode();

            return str_contains($code, 'HIGH-') || str_contains($code, 'EXACT-');
        });
        $highScoreLeads = array_values($highScoreLeads); // 重新索引数组
        $this->assertCount(3, $highScoreLeads);

        // 验证所有线索评分都大于等于80
        foreach ($highScoreLeads as $lead) {
            $this->assertGreaterThanOrEqual(80, $lead->getScore());
        }

        // 验证结果按评分降序排序
        $this->assertEquals(95, $highScoreLeads[0]->getScore());
        $this->assertEquals(85, $highScoreLeads[1]->getScore());
        $this->assertEquals(80, $highScoreLeads[2]->getScore());
    }

    public function testLeadSaveAndRemove(): void
    {
        $repository = $this->getRepository();

        // 测试save方法
        $lead = new Lead();
        $leadCode = 'SAVE-TEST-' . uniqid();
        $lead->setLeadCode($leadCode);
        $lead->setCompanyName('保存测试公司');
        $lead->setContactName('保存测试联系人');
        $lead->setSource(LeadSource::WEBSITE);
        $lead->setStatus(LeadStatus::NEW);
        $lead->setScore(70);

        // 测试不flush的save
        $repository->save($lead, false);
        $foundBeforeFlush = $repository->findOneByLeadCode($leadCode);
        $this->assertNull($foundBeforeFlush); // 还未flush，不应该找到

        // 手动flush
        $entityManager = self::getEntityManager();
        $entityManager->flush();
        $foundAfterFlush = $repository->findOneByLeadCode($leadCode);
        $this->assertNotNull($foundAfterFlush);
        $this->assertEquals($leadCode, $foundAfterFlush->getLeadCode());

        // 测试带flush的save
        $lead2 = new Lead();
        $leadCode2 = 'SAVE-TEST-2-' . uniqid();
        $lead2->setLeadCode($leadCode2);
        $lead2->setCompanyName('保存测试公司2');
        $lead2->setContactName('保存测试联系人2');
        $lead2->setSource(LeadSource::ADVERTISEMENT);
        $lead2->setStatus(LeadStatus::IN_PROGRESS);
        $lead2->setScore(85);

        $repository->save($lead2, true);
        $foundImmediately = $repository->findOneByLeadCode($leadCode2);
        $this->assertNotNull($foundImmediately);
        $this->assertEquals($leadCode2, $foundImmediately->getLeadCode());

        // 测试remove方法
        $repository->remove($foundImmediately, true);
        $removedLead = $repository->findOneByLeadCode($leadCode2);
        $this->assertNull($removedLead);
    }

    public function testLeadSaveWithoutFlush(): void
    {
        $repository = $this->getRepository();

        $lead = new Lead();
        $leadCode = 'NO-FLUSH-TEST-' . uniqid();
        $lead->setLeadCode($leadCode);
        $lead->setCompanyName('无刷新测试公司');
        $lead->setContactName('无刷新测试联系人');
        $lead->setSource(LeadSource::WEBSITE);
        $lead->setStatus(LeadStatus::NEW);
        $lead->setScore(55);

        // 不使用flush的save
        $repository->save($lead, false);

        // persist后实体被管理，但还未flush到数据库
        // 使用查询方法无法找到（因为还没有写入数据库）
        $foundBeforeFlush = $repository->findOneByLeadCode($leadCode);
        $this->assertNull($foundBeforeFlush); // 还未flush，查询找不到

        // flush后应该能找到
        $entityManager = self::getEntityManager();
        $entityManager->flush();
        $foundLead = $repository->findOneByLeadCode($leadCode);
        $this->assertNotNull($foundLead);
        $this->assertEquals('无刷新测试公司', $foundLead->getCompanyName());
    }

    public function testLeadRemoveWithoutFlush(): void
    {
        $repository = $this->getRepository();

        // 先创建一个线索
        $lead = new Lead();
        $leadCode = 'REMOVE-TEST-' . uniqid();
        $lead->setLeadCode($leadCode);
        $lead->setCompanyName('删除测试公司');
        $lead->setContactName('删除测试联系人');
        $lead->setSource(LeadSource::WEBSITE);
        $lead->setStatus(LeadStatus::NEW);
        $lead->setScore(45);

        $repository->save($lead, true);
        $this->assertNotNull($repository->findOneByLeadCode($leadCode));

        // 不使用flush的remove
        $repository->remove($lead, false);

        // 在同一事务中仍然可以找到
        $stillExists = $repository->findOneByLeadCode($leadCode);
        $this->assertNotNull($stillExists);

        // flush后应该被删除
        $entityManager = self::getEntityManager();
        $entityManager->flush();
        $removedLead = $repository->findOneByLeadCode($leadCode);
        $this->assertNull($removedLead);
    }
}
