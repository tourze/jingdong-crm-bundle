<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JingdongCrmBundle\Entity\Product;
use JingdongCrmBundle\Enum\ProductStatus;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, $limit = null, $offset = null)
 */
#[AsRepository(entityClass: Product::class)]
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findByProductCode(string $productCode): ?Product
    {
        /** @var Product|null */
        return $this->createQueryBuilder('p')
            ->andWhere('p.productCode = :productCode')
            ->setParameter('productCode', $productCode)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return Product[]
     */
    public function findByCategory(string $category): array
    {
        /** @var Product[] */
        return $this->createQueryBuilder('p')
            ->andWhere('p.category = :category')
            ->setParameter('category', $category)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Product[]
     */
    public function findOnSaleProducts(): array
    {
        /** @var Product[] */
        return $this->createQueryBuilder('p')
            ->andWhere('p.status = :status')
            ->setParameter('status', ProductStatus::ON_SALE)
            ->orderBy('p.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function save(Product $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Product $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
