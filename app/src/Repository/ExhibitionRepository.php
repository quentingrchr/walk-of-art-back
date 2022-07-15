<?php

namespace App\Repository;

use App\Config\StatusEnum;
use App\Entity\Exhibition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Exhibition|null find($id, $lockMode = null, $lockVersion = null)
 * @method Exhibition|null findOneBy(array $criteria, array $orderBy = null)
 * @method Exhibition[]    findAll()
 * @method Exhibition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ExhibitionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Exhibition::class);
    }

    public function findAllForModaration()
    {

        $query = $this->_em->createQuery('
            SELECT e
            FROM App\Entity\Exhibition e
            WHERE e NOT IN (
                SELECT IDENTITY(es.exhibition)
                FROM App\Entity\ExhibitionStatus es 
                WHERE es.status IN (:statutes)
                GROUP BY es.exhibition)
            ')
            ->setParameter('statutes', [StatusEnum::REFUSED->value, StatusEnum::VALIDATED->value]);
//        dd($query->getSQL());
        return $query->getResult();

//        return $this->createQueryBuilder('e')
//            ->leftJoin(ExhibitionStatus::class,'es',Join::WITH,'es.exhibition = e')
//            ->andWhere('e.statutes.status NOT IN (refused, validated)')
////            ->andWhere('validated NOT IN :statutes')
////            ->setParameter('statutes', [StatusEnum::REFUSED, StatusEnum::VALIDATED])
////            ->orderBy('e.dateStart', 'ASC')
//            ->getQuery()
//            ->getResult()
//        ;
    }


    /*
    public function findOneBySomeField($value): ?Exhibition
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
