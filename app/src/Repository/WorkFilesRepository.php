<?php

namespace App\Repository;

use App\Entity\WorkFiles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WorkFiles|null find($id, $lockMode = null, $lockVersion = null)
 * @method WorkFiles|null findOneBy(array $criteria, array $orderBy = null)
 * @method WorkFiles[]    findAll()
 * @method WorkFiles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkFilesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkFiles::class);
    }

    // /**
    //  * @return WorkFiles[] Returns an array of WorkFiles objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?WorkFiles
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
