<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JingdongCrmBundle\Enum\ContactStatusEnum;
use JingdongCrmBundle\Repository\ContactRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
#[ORM\Table(name: 'jingdong_crm_contact', options: ['comment' => '京东CRM联系人'])]
class Contact implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => '主键ID'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'contacts')]
    #[ORM\JoinColumn(name: 'customer_id', nullable: false)]
    #[Assert\NotNull]
    private ?Customer $customer = null;

    #[ORM\Column(type: Types::STRING, length: 100, options: ['comment' => '联系人姓名'])]
    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    private string $name;

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

    #[ORM\Column(type: Types::STRING, length: 20, nullable: true, options: ['comment' => '手机'])]
    #[Assert\Length(max: 20)]
    private ?string $mobile = null;

    #[IndexColumn]
    #[ORM\Column(name: 'is_primary', type: Types::BOOLEAN, options: ['comment' => '是否主要联系人', 'default' => false])]
    #[Assert\Type(type: 'bool')]
    private bool $isPrimary = false;

    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, length: 20, enumType: ContactStatusEnum::class, options: ['comment' => '状态'])]
    #[Assert\Choice(callback: [ContactStatusEnum::class, 'cases'])]
    private ContactStatusEnum $status = ContactStatusEnum::ACTIVE;

    public function __construct()
    {
        $this->status = ContactStatusEnum::ACTIVE;
        $this->isPrimary = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): void
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

    public function getMobile(): ?string
    {
        return $this->mobile;
    }

    public function setMobile(?string $mobile): void
    {
        $this->mobile = $mobile;
    }

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function getIsPrimary(): bool
    {
        return $this->isPrimary;
    }

    public function setIsPrimary(bool $isPrimary): void
    {
        $this->isPrimary = $isPrimary;
    }

    public function getStatus(): ContactStatusEnum
    {
        return $this->status;
    }

    public function setStatus(ContactStatusEnum $status): void
    {
        $this->status = $status;
    }

    public function __toString(): string
    {
        return sprintf('%s (%s)', $this->name, $this->title ?? '无职位');
    }
}
