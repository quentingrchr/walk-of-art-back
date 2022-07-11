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
        $entityManager = $this->getEntityManager();

        $dateStartDiff = new DateTime($params['dateStart']);
        $dateEndDiff = new DateTime($params['dateEnd']);

        /*dump($params['dateStart'],$params['dateEnd'], $dateEnd->diff($dateStart)->format("%a"), $params['orientation']);
        die;*/

        $query = $entityManager->createQuery(
            '
        SELECT g
        FROM App\Entity\Gallery g, App\Entity\Board b
        LEFT JOIN App\Entity\Exhibition e WITH b.id = e.board
        WHERE g.id = b.gallery        
        AND :orientation = b.orientation
        AND :dateDiff <= g.maxDays
        AND e.board IS null         
        OR (
            g.id = b.gallery
            AND :dateDiff <= g.maxDays 
            AND :orientation = b.orientation 
            AND e.board = b.id 
            AND (e.dateStart NOT BETWEEN :dateStart and :dateEnd) 
            AND (e.dateEnd NOT BETWEEN :dateStart and :dateEnd)
        )
        GROUP BY g
        '
        )->setParameters([
            'dateStart' => $params['dateStart'],
            'dateEnd' => $params['dateEnd'],
            'dateDiff' => intval($dateStartDiff->diff($dateEndDiff)->format("%a")),
            'orientation' => $params['orientation'],
        ]);

        return $query->getResult();

        /*AND (:dateStart NOT BETWEEN e.dateStart and e.dateEnd)
        AND (:dateEnd NOT BETWEEN e.dateStart and e.dateEnd)*/

        // Sql Query used :

        // select gallery.latitude, gallery.longitude, gallery.id, gallery.price, gallery.name
        // from gallery, board, reservation
        // where gallery.id = board.gallery_id
        // and reservation.id = board.reservation_id or board.reservation_id = null
        // and dateStart > reservation.dateEnd or dateEnd < reservation.dateStart
        // and DATEDIFF(Date.now, reservation.dateStart) <= gallery.max_days
        // and orientation = board.orientation
    }
}
