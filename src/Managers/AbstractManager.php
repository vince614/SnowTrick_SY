<?php

namespace App\Managers;

use App\Entity\EntityInterface;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractManager {

    /** @var EntityManagerInterface */
    protected $entityManager;

    /**
     * FigureManager constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Save entity
     *
     * @param EntityInterface $entity
     * @return void
     */
    public function save(EntityInterface $entity): void
    {
        $this->initialise($entity);
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

}