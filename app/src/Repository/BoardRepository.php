<?php

namespace App\Repository;

use App\Entity\Board;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Board|null find($id, $lockMode = null, $lockVersion = null)
 * @method Board|null findOneBy(array $criteria, array $orderBy = null)
 * @method Board[]    findAll()
 * @method Board[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BoardRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Board::class);
    }

    /**
     * Return first board available by gallery, dates, and orientation
     *
     * @return Board|null Returns Board objects
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getBoardAvailableByGalleryByParam(string $galleryId, \DateTime $dateStart, \DateTime $dateEnd, string $orientation): ?Board
    {
        $query = $this->_em->createQuery('
                SELECT b
                FROM App\Entity\Board b
                LEFT JOIN App\Entity\Gallery g WITH g.id = b.gallery
                LEFT JOIN App\Entity\Exhibition e WITH e.board = b.id
                WHERE b.id NOT IN (
                    SELECT IDENTITY(ee.board)
                    FROM App\Entity\Exhibition ee
                    WHERE e.dateStart < :dateEnd OR e.dateEnd > :dateStart
                )
                AND b.gallery = :galleryId
                AND b.orientation  = :orientation
                AND g.maxDays  >= :dateInterval
            ')
            ->setParameters([
                'galleryId' => $galleryId,
                'orientation' => $orientation,
                'dateInterval' => $dateStart->setTime(0,0)->diff($dateEnd->setTime(0,0))->format('%a'),
                'dateStart' => $dateStart->format('Y-m-d'),
                'dateEnd' => $dateEnd->format('Y-m-d')])
            ->setMaxResults(1);

        return $query->getOneOrNullResult();
    }

}