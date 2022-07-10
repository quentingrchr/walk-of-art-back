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

        $dateStart = new DateTime($params['dateStart']);
        $dateEnd = new DateTime($params['dateEnd']);

        $query = $entityManager->createQuery(
            '
        SELECT g
        FROM App\Entity\Gallery g, App\Entity\Board b, App\Entity\Reservation r
        where g.id = b.gallery
        AND ((r.board = b.id and (r.dateEnd < :dateStart or r.dateStart > :dateEnd)) or r.board IS NULL)
        AND :dateDiff <= g.maxDays
        AND :orientation = b.orientation
        GROUP by g.id
        '
        )->setParameters([
            'dateStart' => $dateStart,
            'dateEnd' => $dateEnd,
            'dateDiff' => $dateEnd->diff($dateStart)->format("%a"),
            'orientation' => $params['orientation']
        ]);

        return $query->getResult();

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
