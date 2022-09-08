<?php

namespace App\Managers;

use App\Entity\Figure;
use App\Helper\UniqueSlug;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * Class FigureManager
 */
class FigureManager extends AbstractManager
{

    private UniqueSlug $uniqueSlug;

    /**
     * FigureManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param UniqueSlug $uniqueSlug
     */
    public function __construct(EntityManagerInterface $entityManager, UniqueSlug $uniqueSlug)
    {
        $this->uniqueSlug = $uniqueSlug;
        parent::__construct($entityManager);
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
            $slug = $this->uniqueSlug->getUniqueSLug($entity->getName());
            $currentTime = new DateTimeImmutable();
            $entity
                ->setSlug($slug)
                ->setCreatedAt($currentTime);
        }
    }
}