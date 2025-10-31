<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JingdongCrmBundle\Entity\Lead;
use JingdongCrmBundle\Enum\LeadStatus;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<Lead>
 *
 * @method Lead|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lead|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method Lead[]    findAll()
 * @method Lead[]    findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, int|null $limit = null, int|null $offset = null)
 */
#[AsRepository(entityClass: Lead::class)]
class LeadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lead::class);
    }

    public function save(Lead $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Lead $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * 根据线索编码查找线索
     */
    public function findOneByLeadCode(string $leadCode): ?Lead
    {
        return $this->findOneBy(['leadCode' => $leadCode]);
    }

    /**
     * 根据状态查找线索
     *
     * @return Lead[]
     */
    public function findByStatus(LeadStatus $status): array
    {
        return $this->findBy(['status' => $status], ['createTime' => 'DESC']);
    }

    /**
     * 根据分配人查找线索
     *
     * @return Lead[]
     */
    public function findByAssignedTo(string $assignedTo): array
    {
        return $this->findBy(['assignedTo' => $assignedTo], ['createTime' => 'DESC']);
    }

    /**
     * 根据公司名称搜索线索
     *
     * @return Lead[]
     */
    public function findByCompanyName(string $companyName): array
    {
        /** @var Lead[] */
        return $this->createQueryBuilder('l')
            ->where('l.companyName LIKE :companyName')
            ->setParameter('companyName', '%' . $companyName . '%')
            ->orderBy('l.createTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * 根据评分范围查找线索
     *
     * @return Lead[]
     */
    public function findByScoreRange(?int $minScore = null, ?int $maxScore = null): array
    {
        $qb = $this->createQueryBuilder('l');

        if (null !== $minScore) {
            $qb->andWhere('l.score >= :minScore')
                ->setParameter('minScore', $minScore)
            ;
        }

        if (null !== $maxScore) {
            $qb->andWhere('l.score <= :maxScore')
                ->setParameter('maxScore', $maxScore)
            ;
        }

        /** @var Lead[] */
        return $qb->orderBy('l.score', 'DESC')
            ->addOrderBy('l.createTime', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * 获取高分线索（评分大于等于80分）
     *
     * @return Lead[]
     */
    public function findHighScoreLeads(): array
    {
        return $this->findByScoreRange(80);
    }
}
