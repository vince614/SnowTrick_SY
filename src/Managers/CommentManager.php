<?php

namespace App\Managers;

use App\Entity\Comment;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class CommentManager
 */
class CommentManager extends AbstractManager
{

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
    }

    /**
     * Initialise entity before save
     *
     * @param Comment $entity
     * @throws Exception
     */
    protected function initialise(Comment $entity)
    {
        if (!$entity->getId()) {
            $currentTime = new DateTimeImmutable();
            $entity
                ->setCreatedAt($currentTime)
                ->setValid(false);
        }
    }
}