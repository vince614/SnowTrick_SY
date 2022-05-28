<?php

namespace App\Managers;

use App\Entity\Group;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class GroupManager
 */
class GroupManager extends AbstractManager
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
     * @param Group $entity
     */
    protected function initialise(Group $entity)
    {

    }

}