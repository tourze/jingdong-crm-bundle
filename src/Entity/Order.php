<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JingdongCrmBundle\Enum\OrderStatus;
use JingdongCrmBundle\Repository\OrderRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'jingdong_crm_order', options: ['comment' => '订单管理'])]
#[ORM\UniqueConstraint(name: 'order_number_uniq', columns: ['order_number'])]
class Order implements \Stringable
{
    use TimestampableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[Assert\NotBlank]
    #[Assert\Length(max: 100)]
    #[IndexColumn]
    #[ORM\Column(type: Types::STRING, length: 100, unique: true, options: ['comment' => '订单号'])]
    private string $orderNumber;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Customer::class)]
    #[ORM\JoinColumn(name: 'customer_id', referencedColumnName: 'id', nullable: false)]
    private Customer $customer;

    #[ORM\ManyToOne(targetEntity: Opportunity::class)]
    #[ORM\JoinColumn(name: 'opportunity_id', referencedColumnName: 'id', nullable: true)]
    private ?Opportunity $opportunity = null;

    #[Assert\NotNull]
    #[Assert\Choice(callback: [OrderStatus::class, 'cases'])]
    #[IndexColumn]
    #[ORM\Column(
        type: Types::STRING,
        enumType: OrderStatus::class,
        options: ['comment' => '订单状态', 'default' => 'pending_payment']
    )]
    private OrderStatus $status = OrderStatus::PENDING_PAYMENT;

    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    #[ORM\Column(type: Types::DECIMAL, precision: 12, scale: 2, options: ['comment' => '订单总金额'])]
    private string $totalAmount;

    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    #[ORM\Column(
        type: Types::DECIMAL,
        precision: 12,
        scale: 2,
        options: ['comment' => '已付金额', 'default' => '0.00']
    )]
    private string $paidAmount = '0.00';

    #[Assert\NotNull]
    #[Assert\PositiveOrZero]
    #[ORM\Column(
        type: Types::DECIMAL,
        precision: 12,
        scale: 2,
        options: ['comment' => '优惠金额', 'default' => '0.00']
    )]
    private string $discountAmount = '0.00';

    #[Assert\NotNull]
    #[IndexColumn]
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, options: ['comment' => '下单日期'])]
    private \DateTimeImmutable $orderDate;

    #[Assert\Length(max: 100)]
    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, options: ['comment' => '支付方式'])]
    private ?string $paymentMethod = null;

    #[Assert\Length(max: 1000)]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '收货地址'])]
    private ?string $shippingAddress = null;

    #[Assert\Length(max: 100)]
    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, options: ['comment' => '京东订单ID'])]
    private ?string $jdOrderId = null;

    #[Assert\Length(max: 65535)]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '备注'])]
    private ?string $notes = null;

    public function __construct()
    {
        $this->orderDate = new \DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(string $orderNumber): void
    {
        $this->orderNumber = $orderNumber;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function setCustomer(Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function getOpportunity(): ?Opportunity
    {
        return $this->opportunity;
    }

    public function setOpportunity(?Opportunity $opportunity): void
    {
        $this->opportunity = $opportunity;
    }

    public function getStatus(): OrderStatus
    {
        return $this->status;
    }

    public function setStatus(OrderStatus $status): void
    {
        $this->status = $status;
    }

    public function getTotalAmount(): string
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(string $totalAmount): void
    {
        $this->totalAmount = $totalAmount;
    }

    public function getPaidAmount(): string
    {
        return $this->paidAmount;
    }

    public function setPaidAmount(string $paidAmount): void
    {
        $this->paidAmount = $paidAmount;
    }

    public function getDiscountAmount(): string
    {
        return $this->discountAmount;
    }

    public function setDiscountAmount(string $discountAmount): void
    {
        $this->discountAmount = $discountAmount;
    }

    public function getOrderDate(): \DateTimeImmutable
    {
        return $this->orderDate;
    }

    public function setOrderDate(\DateTimeImmutable $orderDate): void
    {
        $this->orderDate = $orderDate;
    }

    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(?string $paymentMethod): void
    {
        $this->paymentMethod = $paymentMethod;
    }

    public function getShippingAddress(): ?string
    {
        return $this->shippingAddress;
    }

    public function setShippingAddress(?string $shippingAddress): void
    {
        $this->shippingAddress = $shippingAddress;
    }

    public function getJdOrderId(): ?string
    {
        return $this->jdOrderId;
    }

    public function setJdOrderId(?string $jdOrderId): void
    {
        $this->jdOrderId = $jdOrderId;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }

    public function __toString(): string
    {
        return sprintf(
            'Order[%d]: %s - %s (¥%s)',
            $this->id,
            $this->orderNumber ?? 'No Number',
            $this->status->getLabel(),
            $this->totalAmount ?? '0.00'
        );
    }
}
