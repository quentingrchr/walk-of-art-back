<?php

namespace App\Repository;

use App\Entity\Gallery;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Gallery|null find($id, $lockMode = null, $lockVersion = null)
 * @method Gallery|null findOneBy(array $criteria, array $orderBy = null)
 * @method Gallery[]    findAll()
 * @method Gallery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GalleryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Gallery::class);
    }

    public function findAvailableGalleriesByParams($params)
    {
        $dateStartDiff = new DateTime($params['dateStart']);
        $dateEndDiff = new DateTime($params['dateEnd']);

        $query = $this->_em->createQuery('
                SELECT g
                FROM App\Entity\Gallery g
                LEFT JOIN App\Entity\Board b WITH b.gallery = g.id
                LEFT JOIN App\Entity\Exhibition e WITH e.board = b.id
                WHERE b.orientation = :orientation 
                AND g.maxDays >= :dateDiff
                AND b.id NOT IN (
                            SELECT IDENTITY(ee.board)
                            FROM App\Entity\Exhibition ee
                            WHERE e.dateStart < :dateEnd OR e.dateEnd > :dateStart
                ) 
            ')
            ->setParameters([
                'dateStart' => $params['dateStart'],
                'dateEnd' => $params['dateEnd'],
                'dateDiff' => intval($dateStartDiff->diff($dateEndDiff)->format("%a")),
                'orientation' => $params['orientation'],
            ]);

        return $query->getResult();
    }
}
