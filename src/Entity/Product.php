<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JingdongCrmBundle\Enum\ProductStatus;
use JingdongCrmBundle\Repository\ProductRepository;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Tourze\DoctrineIndexedBundle\Attribute\IndexColumn;
use Tourze\DoctrineTimestampBundle\Traits\TimestampableAware;
use Tourze\DoctrineTrackBundle\Attribute\TrackColumn;
use Tourze\DoctrineUserBundle\Traits\BlameableAware;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\Table(name: 'jingdong_crm_product', options: ['comment' => '京东CRM产品表'])]
#[ORM\UniqueConstraint(name: 'uk_product_code', columns: ['product_code'])]
#[UniqueEntity(fields: ['productCode'], message: '产品编码已经存在，请使用不同的编码')]
class Product implements \Stringable
{
    use TimestampableAware;
    use BlameableAware;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER, options: ['comment' => 'ID'])]
    private int $id = 0;

    #[Assert\NotBlank(message: '产品编码不能为空')]
    #[Assert\Length(max: 100, maxMessage: '产品编码长度不能超过100个字符')]
    #[IndexColumn(name: 'product_code')]
    #[TrackColumn]
    #[ORM\Column(name: 'product_code', type: Types::STRING, length: 100, unique: true, options: ['comment' => '产品编码'])]
    private string $productCode = '';

    #[Assert\NotBlank(message: '产品名称不能为空')]
    #[Assert\Length(max: 200, maxMessage: '产品名称长度不能超过200个字符')]
    #[IndexColumn(name: 'name')]
    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 200, options: ['comment' => '产品名称'])]
    private string $name = '';

    #[Assert\Length(max: 100, maxMessage: '产品分类长度不能超过100个字符')]
    #[IndexColumn(name: 'category')]
    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, length: 100, nullable: true, options: ['comment' => '产品分类'])]
    private ?string $category = null;

    #[Assert\Length(max: 5000, maxMessage: '产品描述长度不能超过5000个字符')]
    #[ORM\Column(type: Types::TEXT, nullable: true, options: ['comment' => '产品描述'])]
    private ?string $description = null;

    #[Assert\NotNull(message: '价格不能为空')]
    #[Assert\PositiveOrZero(message: '价格必须大于或等于0')]
    #[TrackColumn]
    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, options: ['comment' => '价格'])]
    private string $price = '0.00';

    #[Assert\Length(max: 50, maxMessage: '单位长度不能超过50个字符')]
    #[ORM\Column(type: Types::STRING, length: 50, nullable: true, options: ['comment' => '单位'])]
    private ?string $unit = null;

    #[Assert\Choice(callback: [ProductStatus::class, 'cases'], message: '状态值无效')]
    #[IndexColumn(name: 'idx_product_status')]
    #[TrackColumn]
    #[ORM\Column(type: Types::STRING, enumType: ProductStatus::class, options: ['default' => 'on_sale', 'comment' => '状态'])]
    private ProductStatus $status = ProductStatus::ON_SALE;

    #[Assert\Length(max: 100, maxMessage: '京东产品ID长度不能超过100个字符')]
    #[IndexColumn(name: 'jd_product_id')]
    #[ORM\Column(name: 'jd_product_id', type: Types::STRING, length: 100, nullable: true, options: ['comment' => '京东产品ID'])]
    private ?string $jdProductId = null;

    public function __construct()
    {
        // TimestampableAware trait will handle createTime and updateTime automatically
    }

    public function __toString(): string
    {
        return '' !== $this->name ? $this->name : $this->productCode;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProductCode(): string
    {
        return $this->productCode;
    }

    public function setProductCode(string $productCode): void
    {
        $this->productCode = $productCode;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): void
    {
        $this->category = $category;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getPrice(): string
    {
        return $this->price;
    }

    public function setPrice(string $price): void
    {
        $this->price = $price;
    }

    public function getUnit(): ?string
    {
        return $this->unit;
    }

    public function setUnit(?string $unit): void
    {
        $this->unit = $unit;
    }

    public function getStatus(): ProductStatus
    {
        return $this->status;
    }

    public function setStatus(ProductStatus $status): void
    {
        $this->status = $status;
    }

    public function getJdProductId(): ?string
    {
        return $this->jdProductId;
    }

    public function setJdProductId(?string $jdProductId): void
    {
        $this->jdProductId = $jdProductId;
    }

    public function isOnSale(): bool
    {
        return $this->status->isOnSale();
    }

    public function isOffShelf(): bool
    {
        return $this->status->isOffShelf();
    }

    public function isOutOfStock(): bool
    {
        return $this->status->isOutOfStock();
    }
}
