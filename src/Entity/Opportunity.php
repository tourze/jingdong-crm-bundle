<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JingdongCrmBundle\Enum\OpportunityStageEnum;
use JingdongCrmBundle\Enum\OpportunityStatusEnum;
use JingdongCrmBundle\Repository\OpportunityRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: OpportunityRepository::class)]
#[ORM\Table(name: 'jingdong_crm_opportunity', options: ['comment' => '销售机会管理'])]
#[ORM\UniqueConstraint(name: 'opportunity_code_uniq', columns: ['opportunity_code'])]
class Opportunity implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, length: 100, unique: true, options: ['comment' => '机会编码'])]
    private string $opportunityCode;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Customer::class)]
    #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'id', nullable: false)]
    private Customer $customer;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '机会名称'])]
    private string $name;

    #[Assert\Length(max: 2000)]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '描述'])]
    private ?string $description = null;

    #[Assert\NotNull]
    #[Assert\Choice(callback: [OpportunityStageEnum::class, 'cases'])]
    #[IndexColumn]
    #[ORM\Column(
        type: Types::STRING,
        enumType: OpportunityStageEnum::class,
        options: ['comment' => '阶段', 'default' => 'identify_needs']
    )]
    private OpportunityStageEnum $stage = OpportunityStageEnum::IDENTIFY_NEEDS;

    #[Assert\Type(type: 'numeric')]
    #[Assert\PositiveOrZero]
    #[ORM\Column(
        type: Types::DECIMAL,
        precision: 12,
        scale: 2,
        nullable: true,
        options: ['comment' => '金额']
    )]
    private ?string $amount = null;

    #[Assert\Range(min: 0, max: 100)]
    #[ORM\Column(type: Types::INTEGER, nullable: true, options: ['comment' => '成交概率(0-100)'])]
    private ?int $probability = null;

    #[Assert\Date]
    #[ORM\Column(type: Types::DATE_IMMUTABLE, nullable: true, options: ['comment' => '预期成交时间'])]
    private ?\DateTimeInterface $expectedCloseDate = null;

    #[Assert\Length(max: 100)]
    #[IndexColumn(name: 'idx_opportunity_assigned_to')]
    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, options: ['comment' => '负责人'])]
    private ?string $assignedTo = null;

    #[Assert\Length(max: 100)]
    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, options: ['comment' => '来源'])]
    private ?string $source = null;

    #[Assert\NotNull]
    #[Assert\Choice(callback: [OpportunityStatusEnum::class, 'cases'])]
    #[IndexColumn(name: 'idx_opportunity_status')]
    #[ORM\Column(
        type: Types::STRING,
        enumType: OpportunityStatusEnum::class,
        options: ['comment' => '状态', 'default' => 'active']
    )]
    private OpportunityStatusEnum $status = OpportunityStatusEnum::ACTIVE;

    public function __construct()
    {
        // TimestampableAware trait will handle createTime and updateTime automatically
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getOpportunityCode(): string
    {
        return $this->opportunityCode;
    }

    public function setOpportunityCode(string $opportunityCode): void
    {
        $this->opportunityCode = $opportunityCode;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getStage(): OpportunityStageEnum
    {
        return $this->stage;
    }

    public function setStage(OpportunityStageEnum $stage): void
    {
        $this->stage = $stage;
    }

    public function getAmount(): ?string
    {
        return $this->amount;
    }

    public function setAmount(?string $amount): void
    {
        $this->amount = $amount;
    }

    public function getProbability(): ?int
    {
        return $this->probability;
    }

    public function setProbability(?int $probability): void
    {
        $this->probability = $probability;
    }

    public function getExpectedCloseDate(): ?\DateTimeInterface
    {
        return $this->expectedCloseDate;
    }

    public function setExpectedCloseDate(?\DateTimeInterface $expectedCloseDate): void
    {
        $this->expectedCloseDate = $expectedCloseDate;
    }

    public function getAssignedTo(): ?string
    {
        return $this->assignedTo;
    }

    public function setAssignedTo(?string $assignedTo): void
    {
        $this->assignedTo = $assignedTo;
    }

    public function getSource(): ?string
    {
        return $this->source;
    }

    public function setSource(?string $source): void
    {
        $this->source = $source;
    }

    public function getStatus(): OpportunityStatusEnum
    {
        return $this->status;
    }

    public function setStatus(OpportunityStatusEnum $status): void
    {
        $this->status = $status;
    }

    public function __toString(): string
    {
        return sprintf(
            'Opportunity[%d]: %s (%s) - %s',
            $this->id,
            $this->name ?? 'Unnamed',
            $this->opportunityCode ?? 'No Code',
            $this->stage->getLabel()
        );
    }
}
