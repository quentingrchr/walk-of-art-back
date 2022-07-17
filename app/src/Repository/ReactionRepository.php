<?php

namespace App\Repository;

use App\Entity\Reaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Reaction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reaction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reaction[]    findAll()
 * @method Reaction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reaction::class);
    }

    /**
     * @param string $exhibitionId
     * @return int|mixed|string
     */
    public function findReactionsByExhibition(string $exhibitionId)
    {
        $query = $this->_em->createQuery('
                SELECT r.reaction, COUNT(r.id)
                FROM App\Entity\Reaction r
                LEFT JOIN App\Entity\Exhibition e WITH e.id = r.exhibition
                WHERE r.exhibition = :exhibitionId
                GROUP BY r.reaction 
            ')
            ->setParameters([
                'exhibitionId' => $exhibitionId ]);

        return $query->getResult();
    }
}