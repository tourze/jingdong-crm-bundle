<?php

declare(strict_types=1);

namespace JingdongCrmBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JingdongCrmBundle\Entity\Contact;
use JingdongCrmBundle\Entity\Customer;
use JingdongCrmBundle\Enum\ContactStatusEnum;
use Tourze\PHPUnitSymfonyKernelTest\Attribute\AsRepository;

/**
 * @extends ServiceEntityRepository<Contact>
 *
 * @method Contact|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contact|null findOneBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null)
 * @method Contact[]    findAll()
 * @method Contact[]    findBy(array<string, mixed> $criteria, array<string, string>|null $orderBy = null, int|null $limit = null, int|null $offset = null)
 */
#[AsRepository(entityClass: Contact::class)]
class ContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contact::class);
    }

    /**
     * @return Contact[]
     */
    public function findByCustomer(Customer $customer): array
    {
        /** @var Contact[] */
        return $this->createQueryBuilder('c')
            ->andWhere('c.customer = :customer')
            ->setParameter('customer', $customer)
            ->orderBy('c.isPrimary', 'DESC')
            ->addOrderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findPrimaryContactByCustomer(Customer $customer): ?Contact
    {
        /** @var Contact|null */
        return $this->createQueryBuilder('c')
            ->andWhere('c.customer = :customer')
            ->andWhere('c.isPrimary = :isPrimary')
            ->setParameter('customer', $customer)
            ->setParameter('isPrimary', true)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @return Contact[]
     */
    public function findActiveContacts(): array
    {
        /** @var Contact[] */
        return $this->createQueryBuilder('c')
            ->andWhere('c.status = :status')
            ->setParameter('status', ContactStatusEnum::ACTIVE)
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return Contact[]
     */
    public function findByStatus(ContactStatusEnum $status): array
    {
        /** @var Contact[] */
        return $this->createQueryBuilder('c')
            ->andWhere('c.status = :status')
            ->setParameter('status', $status)
            ->orderBy('c.name', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findByEmail(string $email): ?Contact
    {
        /** @var Contact|null */
        return $this->createQueryBuilder('c')
            ->andWhere('c.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findByPhone(string $phone): ?Contact
    {
        /** @var Contact|null */
        return $this->createQueryBuilder('c')
            ->andWhere('c.phone = :phone OR c.mobile = :phone')
            ->setParameter('phone', $phone)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function save(Contact $entity, bool $flush = true): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Contact $entity, bool $flush = true): void
    {
        $this->getEntityManager()->remove($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
