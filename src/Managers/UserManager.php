<?php

namespace App\Managers;

use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

/**
 * Class UserManager
 */
class UserManager extends AbstractManager
{

    /**
     * FigureManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);
    }

    /**
     * Initialise entity before save
     *
     * @param User $entity
     * @throws Exception
     */
    protected function initialise(User $entity)
    {
        if (!$entity->getId()) {
            $currentTime = new DateTimeImmutable();
            $entity
                ->setActived(false)
                ->setToken('token')
                ->setToken($currentTime)
                ->setCreatedAt($currentTime);


        }
    }
}