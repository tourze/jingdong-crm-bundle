<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JingdongCrmBundle\Entity\Customer;
use JingdongCrmBundle\Entity\Opportunity;
use JingdongCrmBundle\Entity\Order;
use JingdongCrmBundle\Enum\OrderStatus;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<Order>
 *
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, int|null $limit = null, int|null $offset = null)
 */
#[AsRepository(entityClass: Order::class)]
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findByOrderNumber(string $orderNumber): ?Order
    {
        /** @var Order|null */
        return $this->createQueryBuilder('o')
            ->andWhere('o.orderNumber = :orderNumber')
            ->setParameter('orderNumber', $orderNumber)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByJdOrderId(string $jdOrderId): ?Order
    {
        /** @var Order|null */
        return $this->createQueryBuilder('o')
            ->andWhere('o.jdOrderId = :jdOrderId')
            ->setParameter('jdOrderId', $jdOrderId)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return Order[]
     */
    public function findByCustomer(Customer $customer): array
    {
        /** @var Order[] */
        return $this->createQueryBuilder('o')
            ->andWhere('o.customer = :customer')
            ->setParameter('customer', $customer)
            ->orderBy('o.orderDate', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Order[]
     */
    public function findByOpportunity(Opportunity $opportunity): array
    {
        /** @var Order[] */
        return $this->createQueryBuilder('o')
            ->andWhere('o.opportunity = :opportunity')
            ->setParameter('opportunity', $opportunity)
            ->orderBy('o.orderDate', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Order[]
     */
    public function findByStatus(OrderStatus $status): array
    {
        /** @var Order[] */
        return $this->createQueryBuilder('o')
            ->andWhere('o.status = :status')
            ->setParameter('status', $status)
            ->orderBy('o.orderDate', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Order[]
     */
    public function findOrdersByDateRange(\DateTime $startDate, \DateTime $endDate): array
    {
        /** @var Order[] */
        return $this->createQueryBuilder('o')
            ->andWhere('o.orderDate >= :startDate')
            ->andWhere('o.orderDate <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('o.orderDate', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Order[]
     */
    public function findPendingOrders(): array
    {
        return $this->findByStatus(OrderStatus::PENDING_PAYMENT);
    }

    /**
     * @return Order[]
     */
    public function findCompletedOrders(): array
    {
        return $this->findByStatus(OrderStatus::COMPLETED);
    }

    public function getTotalAmountByCustomer(Customer $customer): string
    {
        /** @var float|int|string|null $result */
        $result = $this->createQueryBuilder('o')
            ->select('SUM(o.totalAmount) as total')
            ->andWhere('o.customer = :customer')
            ->andWhere('o.status IN (:completedStatuses)')
            ->setParameter('customer', $customer)
            ->setParameter('completedStatuses', [OrderStatus::PAID, OrderStatus::SHIPPING, OrderStatus::COMPLETED])
            ->getQuery()
            ->getSingleScalarResult()
        ;

        return null === $result ? '0.00' : number_format((float) $result, 2, '.', '');
    }

    public function save(Order $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Order $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
