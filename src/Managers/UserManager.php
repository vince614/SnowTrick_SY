<?php

namespace App\Managers;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Exception;

/**
 * Class UserManager
 */
class UserManager extends AbstractManager
{
    /** @var UserRepository  */
    protected $_userRepository;

    /**
     * FigureManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     */
    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $this->_userRepository = $userRepository;
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
                ->setToken($this->_generateToken())
                ->setTokenCreatedAt($currentTime)
                ->setCreatedAt($currentTime);
        }
    }

    /**
     * Generate random user token verification
     *
     * @throws Exception
     */
    private function _generateToken(): string
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }
}