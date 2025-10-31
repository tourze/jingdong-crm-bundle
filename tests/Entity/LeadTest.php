<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Tests\Entity;

use JingdongCrmBundle\Entity\Lead;
use JingdongCrmBundle\Enum\LeadSource;
use JingdongCrmBundle\Enum\LeadStatus;
use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;

/**
 * @internal
 */
#[CoversClass(Lead::class)]
final class LeadTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new Lead();
    }

    /**
     * @return array<array{string, mixed}>
     */
    public static function propertiesProvider(): array
    {
        return [
            ['leadCode', 'LEAD001'],
            ['companyName', 'Test Company'],
            ['contactName', 'John Doe'],
            ['title', 'Manager'],
            ['email', 'john@example.com'],
            ['phone', '123-456-7890'],
            ['source', LeadSource::WEBSITE],
            ['status', LeadStatus::IN_PROGRESS],
            ['score', 85],
            ['notes', 'Test notes'],
            ['assignedTo', 'sales@example.com'],
        ];
    }

    public function testConstruct(): void
    {
        $lead = new Lead();

        self::assertSame(LeadStatus::NEW, $lead->getStatus());
    }

    public function testGetSetLeadCode(): void
    {
        $lead = new Lead();
        $leadCode = 'LEAD001';

        $lead->setLeadCode($leadCode);
        self::assertSame($leadCode, $lead->getLeadCode());
    }

    public function testGetSetCompanyName(): void
    {
        $lead = new Lead();
        $companyName = 'Test Company';

        $lead->setCompanyName($companyName);
        self::assertSame($companyName, $lead->getCompanyName());
    }

    public function testGetSetContactName(): void
    {
        $lead = new Lead();
        $contactName = 'John Doe';

        $lead->setContactName($contactName);
        self::assertSame($contactName, $lead->getContactName());
    }

    public function testGetSetTitle(): void
    {
        $lead = new Lead();
        $title = 'Manager';

        $lead->setTitle($title);
        self::assertSame($title, $lead->getTitle());

        $lead->setTitle(null);
        self::assertNull($lead->getTitle());
    }

    public function testGetSetEmail(): void
    {
        $lead = new Lead();
        $email = 'john@example.com';

        $lead->setEmail($email);
        self::assertSame($email, $lead->getEmail());

        $lead->setEmail(null);
        self::assertNull($lead->getEmail());
    }

    public function testGetSetPhone(): void
    {
        $lead = new Lead();
        $phone = '123-456-7890';

        $lead->setPhone($phone);
        self::assertSame($phone, $lead->getPhone());

        $lead->setPhone(null);
        self::assertNull($lead->getPhone());
    }

    public function testGetSetSource(): void
    {
        $lead = new Lead();
        $source = LeadSource::WEBSITE;

        $lead->setSource($source);
        self::assertSame($source, $lead->getSource());
    }

    public function testGetSetStatus(): void
    {
        $lead = new Lead();

        self::assertSame(LeadStatus::NEW, $lead->getStatus());

        $lead->setStatus(LeadStatus::IN_PROGRESS);
        self::assertSame(LeadStatus::IN_PROGRESS, $lead->getStatus());
    }

    public function testGetSetScore(): void
    {
        $lead = new Lead();
        $score = 85;

        $lead->setScore($score);
        self::assertSame($score, $lead->getScore());

        $lead->setScore(null);
        self::assertNull($lead->getScore());
    }

    public function testGetSetNotes(): void
    {
        $lead = new Lead();
        $notes = 'Test notes';

        $lead->setNotes($notes);
        self::assertSame($notes, $lead->getNotes());

        $lead->setNotes(null);
        self::assertNull($lead->getNotes());
    }

    public function testGetSetAssignedTo(): void
    {
        $lead = new Lead();
        $assignedTo = 'sales@example.com';

        $lead->setAssignedTo($assignedTo);
        self::assertSame($assignedTo, $lead->getAssignedTo());

        $lead->setAssignedTo(null);
        self::assertNull($lead->getAssignedTo());
    }

    public function testGetId(): void
    {
        $lead = new Lead();

        self::assertNull($lead->getId());
    }

    public function testToString(): void
    {
        $lead = new Lead();
        $lead->setCompanyName('Test Company');
        $lead->setContactName('John Doe');

        self::assertSame('Test Company (John Doe)', (string) $lead);
    }
}
