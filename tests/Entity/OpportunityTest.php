<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Tests\Entity;

use JingdongCrmBundle\Entity\Customer;
use JingdongCrmBundle\Entity\Opportunity;
use JingdongCrmBundle\Enum\OpportunityStageEnum;
use JingdongCrmBundle\Enum\OpportunityStatusEnum;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Opportunity::class)]
final class OpportunityTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Opportunity();
    }

    /**
     * @return array<array{string, mixed}>
     */
    public static function propertiesProvider(): array
    {
        return [
            ['opportunityCode', 'OPP001'],
            ['name', 'Test Opportunity'],
            ['description', 'Test description'],
            ['stage', OpportunityStageEnum::SOLUTION_DESIGN],
            ['amount', '1000.00'],
            ['probability', 75],
            ['expectedCloseDate', new \DateTimeImmutable('2023-12-31')],
            ['assignedTo', 'sales@example.com'],
            ['source', 'website'],
            ['status', OpportunityStatusEnum::WON],
        ];
    }

    public function testDefaultValues(): void
    {
        $opportunity = new Opportunity();

        self::assertSame(OpportunityStageEnum::IDENTIFY_NEEDS, $opportunity->getStage());
        self::assertSame(OpportunityStatusEnum::ACTIVE, $opportunity->getStatus());
        self::assertSame(0, $opportunity->getId());
    }

    public function testGetSetOpportunityCode(): void
    {
        $opportunity = new Opportunity();
        $opportunityCode = 'OPP001';

        $opportunity->setOpportunityCode($opportunityCode);
        self::assertSame($opportunityCode, $opportunity->getOpportunityCode());
    }

    public function testGetSetCustomer(): void
    {
        $opportunity = new Opportunity();
        $customer = new Customer();

        $opportunity->setCustomer($customer);
        self::assertSame($customer, $opportunity->getCustomer());
    }

    public function testGetSetName(): void
    {
        $opportunity = new Opportunity();
        $name = 'Test Opportunity';

        $opportunity->setName($name);
        self::assertSame($name, $opportunity->getName());
    }

    public function testGetSetDescription(): void
    {
        $opportunity = new Opportunity();
        $description = 'Test description';

        $opportunity->setDescription($description);
        self::assertSame($description, $opportunity->getDescription());

        $opportunity->setDescription(null);
        self::assertNull($opportunity->getDescription());
    }

    public function testGetSetStage(): void
    {
        $opportunity = new Opportunity();

        self::assertSame(OpportunityStageEnum::IDENTIFY_NEEDS, $opportunity->getStage());

        $opportunity->setStage(OpportunityStageEnum::SOLUTION_DESIGN);
        self::assertSame(OpportunityStageEnum::SOLUTION_DESIGN, $opportunity->getStage());
    }

    public function testGetSetAmount(): void
    {
        $opportunity = new Opportunity();
        $amount = '1000.00';

        $opportunity->setAmount($amount);
        self::assertSame($amount, $opportunity->getAmount());

        $opportunity->setAmount(null);
        self::assertNull($opportunity->getAmount());
    }

    public function testGetSetProbability(): void
    {
        $opportunity = new Opportunity();
        $probability = 75;

        $opportunity->setProbability($probability);
        self::assertSame($probability, $opportunity->getProbability());

        $opportunity->setProbability(null);
        self::assertNull($opportunity->getProbability());
    }

    public function testGetSetExpectedCloseDate(): void
    {
        $opportunity = new Opportunity();
        $date = new \DateTimeImmutable('2023-12-31');

        $opportunity->setExpectedCloseDate($date);
        self::assertSame($date, $opportunity->getExpectedCloseDate());

        $opportunity->setExpectedCloseDate(null);
        self::assertNull($opportunity->getExpectedCloseDate());
    }

    public function testGetSetAssignedTo(): void
    {
        $opportunity = new Opportunity();
        $assignedTo = 'sales@example.com';

        $opportunity->setAssignedTo($assignedTo);
        self::assertSame($assignedTo, $opportunity->getAssignedTo());

        $opportunity->setAssignedTo(null);
        self::assertNull($opportunity->getAssignedTo());
    }

    public function testGetSetSource(): void
    {
        $opportunity = new Opportunity();
        $source = 'website';

        $opportunity->setSource($source);
        self::assertSame($source, $opportunity->getSource());

        $opportunity->setSource(null);
        self::assertNull($opportunity->getSource());
    }

    public function testGetSetStatus(): void
    {
        $opportunity = new Opportunity();

        self::assertSame(OpportunityStatusEnum::ACTIVE, $opportunity->getStatus());

        $opportunity->setStatus(OpportunityStatusEnum::WON);
        self::assertSame(OpportunityStatusEnum::WON, $opportunity->getStatus());
    }

    public function testGetId(): void
    {
        $opportunity = new Opportunity();

        self::assertSame(0, $opportunity->getId());
    }

    public function testToString(): void
    {
        $opportunity = new Opportunity();
        $opportunity->setName('Test Opportunity');
        $opportunity->setOpportunityCode('OPP001');
        $opportunity->setStage(OpportunityStageEnum::SOLUTION_DESIGN);

        // getId() returns 0 for new entities, stage needs getLabel() method
        $expectedString = sprintf(
            'Opportunity[0]: Test Opportunity (OPP001) - %s',
            $opportunity->getStage()->getLabel()
        );
        self::assertSame($expectedString, (string) $opportunity);
    }

    public function testToStringWithDefaults(): void
    {
        $opportunity = new Opportunity();

        // Test fallback values in __toString
        $expectedString = sprintf(
            'Opportunity[0]: Unnamed (No Code) - %s',
            $opportunity->getStage()->getLabel()
        );
        self::assertSame($expectedString, (string) $opportunity);
    }
}
