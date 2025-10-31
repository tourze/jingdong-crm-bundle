<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JingdongCrmBundle\Entity\Customer;
use JingdongCrmBundle\Enum\CustomerStatusEnum;
use JingdongCrmBundle\Enum\CustomerTypeEnum;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<Customer>
 *
 * @method Customer|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customer|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method Customer[]    findAll()
 * @method Customer[]    findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, int|null $limit = null, int|null $offset = null)
 */
#[AsRepository(entityClass: Customer::class)]
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }

    public function findByCustomerCode(string $customerCode): ?Customer
    {
        /** @var Customer|null */
        return $this->createQueryBuilder('c')
            ->andWhere('c.customerCode = :customerCode')
            ->setParameter('customerCode', $customerCode)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByJdCustomerId(string $jdCustomerId): ?Customer
    {
        /** @var Customer|null */
        return $this->createQueryBuilder('c')
            ->andWhere('c.jdCustomerId = :jdCustomerId')
            ->setParameter('jdCustomerId', $jdCustomerId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return Customer[]
     */
    public function findActiveCustomers(): array
    {
        /** @var Customer[] */
        return $this->createQueryBuilder('c')
            ->andWhere('c.status = :status')
            ->setParameter('status', CustomerStatusEnum::ACTIVE)
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Customer[]
     */
    public function findByType(CustomerTypeEnum $type): array
    {
        /** @var Customer[] */
        return $this->createQueryBuilder('c')
            ->andWhere('c.type = :type')
            ->setParameter('type', $type)
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function save(Customer $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Customer $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
