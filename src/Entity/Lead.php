<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JingdongCrmBundle\Enum\LeadSource;
use JingdongCrmBundle\Enum\LeadStatus;
use JingdongCrmBundle\Repository\LeadRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: LeadRepository::class)]
#[ORM\Table(name: 'jingdong_crm_lead', options: ['comment' => '京东CRM销售线索'])]
#[ORM\UniqueConstraint(name: 'uniq_lead_code', columns: ['lead_code'])]
class Lead implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, length: 50, unique: true, options: ['comment' => '线索编码'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 50)]
    private string $leadCode;

    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, length: 200, options: ['comment' => '公司名称'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 200)]
    private string $companyName;

    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '联系人姓名'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private string $contactName;

    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, options: ['comment' => '职位'])]
    #[Assert\Length(max: 100)]
    private ?string $title = null;

    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '邮箱'])]
    #[Assert\Email]
    #[Assert\Length(max: 255)]
    private ?string $email = null;

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true, options: ['comment' => '电话'])]
    #[Assert\Length(max: 20)]
    private ?string $phone = null;

    #[IndexColumn]
    #[ORM\Column(enumType: LeadSource::class, options: ['comment' => '线索来源'])]
    #[Assert\NotNull]
    #[Assert\Choice(callback: [LeadSource::class, 'cases'])]
    private LeadSource $source;

    #[IndexColumn]
    #[ORM\Column(enumType: LeadStatus::class, options: ['comment' => '线索状态'])]
    #[Assert\NotNull]
    #[Assert\Choice(callback: [LeadStatus::class, 'cases'])]
    private LeadStatus $status;

    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '评分(0-100)'])]
    #[Assert\Range(min: 0, max: 100)]
    private ?int $score = null;

    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '备注'])]
    #[Assert\Length(max: 65535)]
    private ?string $notes = null;

    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, options: ['comment' => '分配给'])]
    #[Assert\Length(max: 100)]
    private ?string $assignedTo = null;

    public function __construct()
    {
        $this->status = LeadStatus::NEW;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLeadCode(): string
    {
        return $this->leadCode;
    }

    public function setLeadCode(string $leadCode): void
    {
        $this->leadCode = $leadCode;
    }

    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): void
    {
        $this->companyName = $companyName;
    }

    public function getContactName(): string
    {
        return $this->contactName;
    }

    public function setContactName(string $contactName): void
    {
        $this->contactName = $contactName;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    public function getSource(): LeadSource
    {
        return $this->source;
    }

    public function setSource(LeadSource $source): void
    {
        $this->source = $source;
    }

    public function getStatus(): LeadStatus
    {
        return $this->status;
    }

    public function setStatus(LeadStatus $status): void
    {
        $this->status = $status;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(?int $score): void
    {
        $this->score = $score;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }

    public function getAssignedTo(): ?string
    {
        return $this->assignedTo;
    }

    public function setAssignedTo(?string $assignedTo): void
    {
        $this->assignedTo = $assignedTo;
    }

    public function __toString(): string
    {
        return sprintf('%s (%s)', $this->companyName, $this->contactName);
    }
}
