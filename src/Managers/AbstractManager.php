<?php

namespace App\Managers;

use Doctrine\ORM\EntityManagerInterface;

class AbstractManager {

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

}