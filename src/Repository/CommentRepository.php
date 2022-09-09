<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comment::class);
    }


    /**
     * Find actived comments
     *
     * @param $figureId
     * @return Comment[] Returns an array of Comment objects
     */
    public function findActivatedComment($figureId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.valid = :valid')
            ->andWhere('c.figure = :figure_id')
            ->setParameter('valid', true)
            ->setParameter('figure_id', $figureId)
            ->orderBy('c.created_at', 'DESC')
            ->getQuery()
            ->getResult()
            ;
    }
}
