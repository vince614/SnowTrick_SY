<?php

namespace App\Managers;

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
     * Save entity
     * @param $entity
     */
    public function save($entity)
    {
        $this->initialise($entity);
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * Initialise entity before save
     *
     * @param $entity
     */
    protected function initialise($entity)
    {

    }

}