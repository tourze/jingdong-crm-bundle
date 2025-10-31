<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JingdongCrmBundle\Entity\Customer;
use JingdongCrmBundle\Entity\Opportunity;
use JingdongCrmBundle\Enum\OpportunityStageEnum;
use JingdongCrmBundle\Enum\OpportunityStatusEnum;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<Opportunity>
 *
 * @method Opportunity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Opportunity|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method Opportunity[]    findAll()
 * @method Opportunity[]    findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, int|null $limit = null, int|null $offset = null)
 */
#[AsRepository(entityClass: Opportunity::class)]
class OpportunityRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Opportunity::class);
    }

    public function findByOpportunityCode(string $opportunityCode): ?Opportunity
    {
        return $this->findOneBy(['opportunityCode' => $opportunityCode]);
    }

    /**
     * @return Opportunity[]
     */
    public function findActiveOpportunities(): array
    {
        /** @var Opportunity[] */
        return $this->createQueryBuilder('o')
            ->andWhere('o.status = :status')
            ->setParameter('status', OpportunityStatusEnum::ACTIVE)
            ->orderBy('o.expectedCloseDate', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Opportunity[]
     */
    public function findByCustomer(Customer $customer): array
    {
        /** @var Opportunity[] */
        return $this->createQueryBuilder('o')
            ->andWhere('o.customer = :customer')
            ->setParameter('customer', $customer)
            ->orderBy('o.createTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Opportunity[]
     */
    public function findByStage(OpportunityStageEnum $stage): array
    {
        /** @var Opportunity[] */
        return $this->createQueryBuilder('o')
            ->andWhere('o.stage = :stage')
            ->setParameter('stage', $stage)
            ->orderBy('o.expectedCloseDate', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Opportunity[]
     */
    public function findByAssignedTo(string $assignedTo): array
    {
        /** @var Opportunity[] */
        return $this->createQueryBuilder('o')
            ->andWhere('o.assignedTo = :assignedTo')
            ->setParameter('assignedTo', $assignedTo)
            ->orderBy('o.expectedCloseDate', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function save(Opportunity $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Opportunity $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
