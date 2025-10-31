<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JingdongCrmBundle\Enum\CustomerStatusEnum;
use JingdongCrmBundle\Enum\CustomerTypeEnum;
use JingdongCrmBundle\Repository\CustomerRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
#[ORM\Table(name: 'jingdong_crm_customer', options: ['comment' => '客户管理'])]
#[ORM\UniqueConstraint(name: 'customer_code_uniq', columns: ['customer_code'])]
#[ORM\UniqueConstraint(name: 'jd_customer_id_uniq', columns: ['jd_customer_id'])]
class Customer implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, length: 100, unique: true, options: ['comment' => '客户编码'])]
    private string $customerCode;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: Types::STRING, length: 255, options: ['comment' => '客户名称'])]
    private string $name;

    #[Assert\NotNull]
    #[Assert\Choice(callback: [CustomerTypeEnum::class, 'cases'])]
    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, enumType: CustomerTypeEnum::class, options: ['comment' => '客户类型'])]
    private CustomerTypeEnum $type = CustomerTypeEnum::INDIVIDUAL;

    #[Assert\Email]
    #[Assert\Length(max: 255)]
    #[ORM\Column(type: Types::STRING, length: 255, nullable: true, options: ['comment' => '邮箱'])]
    private ?string $email = null;

    #[Assert\Length(max: 50)]
    #[ORM\Column(type: Types::STRING, length: 50, nullable: true, options: ['comment' => '电话'])]
    private ?string $phone = null;

    #[Assert\Length(max: 1000)]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '地址'])]
    private ?string $address = null;

    #[Assert\NotNull]
    #[Assert\Choice(callback: [CustomerStatusEnum::class, 'cases'])]
    #[IndexColumn]
    #[ORM\Column(
        type: Types::STRING,
        enumType: CustomerStatusEnum::class,
        options: ['comment' => '状态', 'default' => 'active']
    )]
    private CustomerStatusEnum $status = CustomerStatusEnum::ACTIVE;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, length: 100, unique: true, options: ['comment' => '京东客户ID'])]
    private string $jdCustomerId;

    public function getId(): int
    {
        return $this->id;
    }

    public function getCustomerCode(): string
    {
        return $this->customerCode;
    }

    public function setCustomerCode(string $customerCode): void
    {
        $this->customerCode = $customerCode;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getType(): CustomerTypeEnum
    {
        return $this->type;
    }

    public function setType(CustomerTypeEnum $type): void
    {
        $this->type = $type;
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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    public function getStatus(): CustomerStatusEnum
    {
        return $this->status;
    }

    public function setStatus(CustomerStatusEnum $status): void
    {
        $this->status = $status;
    }

    public function getJdCustomerId(): string
    {
        return $this->jdCustomerId;
    }

    public function setJdCustomerId(string $jdCustomerId): void
    {
        $this->jdCustomerId = $jdCustomerId;
    }

    public function __toString(): string
    {
        return sprintf('Customer[%d]: %s (%s)', $this->id, $this->name ?? 'Unnamed', $this->customerCode ?? 'No Code');
    }
}
