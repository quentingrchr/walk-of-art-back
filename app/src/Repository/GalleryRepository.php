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
            AND (reservation.date_end < CAST(:date_start AS DATE) or reservation.date_start > CAST(:date_end AS DATE))
            AND :date_diff <= gallery.max_days
            AND :orientation = board.orientation
            GROUP by gallery.id
            ';

        $date_start = new DateTime($params['date_start']);
        $date_end = new DateTime($params['date_end']);

        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery([
            'date_start' => $params['date_start'],
            'date_end' => $params['date_end'],
            'date_diff' => $date_end->diff($date_start)->format("%a"),
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
        // and date_start > reservation.dateEnd or date_end < reservation.dateStart
        // and DATEDIFF(Date.now, reservation.dateStart) <= gallery.max_days
        // and orientation = board.orientation
    }
}
