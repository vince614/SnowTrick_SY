<?php

namespace App\Managers;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Exception;

/**
 * Class UserManager
 */
class UserManager extends AbstractManager
{

    /**
     * Roles
     */
    const ROLE_USER = "ROLE_USER";
    const ROLE_ADMIN = "ROLE_ADMIN";

    private UserPasswordHasherInterface $passwordEncoder;

    /**
     * FigureManager constructor.
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     */
    public function __construct(EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
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
            $this->encodePassword($entity);
            $entity
                ->setActived(false)
                ->setToken($this->_generateToken())
                ->setRoles([self::ROLE_USER])
                ->setTokenCreatedAt($currentTime)
                ->setCreatedAt($currentTime);
        }
    }

    /**
     * Encode password of user
     *
     * @param User $user
     * @return void
     */
    private function encodePassword(User $user): void
    {
        $user->setPassword(
            $this->passwordEncoder->hashPassword(
                $user,
                $user->getPassword()
            )
        );
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