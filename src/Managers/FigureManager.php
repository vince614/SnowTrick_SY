<?php

namespace App\Managers;

use App\Entity\Figure;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Class FigureManager
 */
class FigureManager extends AbstractManager
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
     * @param Figure $entity
     * @throws Exception
     */
    public function save(Figure $entity)
    {
        $this->initialise($entity);
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * Initialise entity before save
     *
     * @param Figure $entity
     * @throws Exception
     */
    protected function initialise(Figure $entity)
    {
        if (!$entity->getId()) {
            $currentTime = new DateTimeImmutable();
            $entity->setCreatedAt($currentTime);
        }
    }
}