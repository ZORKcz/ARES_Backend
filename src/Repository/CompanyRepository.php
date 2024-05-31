<?php

namespace App\Repository;

use App\Entity\Company;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CompanyRepository extends ServiceEntityRepository
{
    public const TRESHOLD = '-30 days';

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Company::class);
    }

    /**
     * @return Company[]
     */
    public function findOldRecords(int $limit): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.updatedAt < :date')
            ->setParameter('date', new \DateTime(self::TRESHOLD))
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Company[]
     */
    public function findNewRecords(int $limit, string $ico): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.updatedAt > :date')
            ->andWhere('c.ico = :ico')
            ->setParameter('date', new \DateTime(self::TRESHOLD))
            ->setParameter('ico', $ico)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
