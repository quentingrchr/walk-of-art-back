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

    public function findByParams($params)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql =  '
            SELECT gallery.*
            FROM gallery, board, reservation
            where gallery.id = board.gallery_id
            AND (reservation.id = board.reservation_id or board.reservation_id IS null)
            AND (reservation.date_end < CAST(:dateStart AS DATE) or reservation.date_start > CAST(:dateEnd AS DATE))
            AND :dateDiff <= gallery.max_days
            AND :orientation = board.orientation
            GROUP by gallery.id
            ';

        $dateStart = new DateTime($params['dateStart']);
        $dateEnd = new DateTime($params['dateEnd']);

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery([
            'dateStart' => $params['dateStart'],
            'dateEnd' => $params['dateEnd'],
            'dateDiff' => $dateEnd->diff($dateStart)->format("%a"),
            'orientation' => $params['orientation']
        ]);

        try {
            return $resultSet->fetchAllAssociative();
        } catch (Exception $e) {
            throw new \RuntimeException('Query error : '. $e);
        }

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
