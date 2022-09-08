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
            $randomAvatar = $this->retrieveRedirectUrl('https://picsum.photos/300/300');
            $entity
                ->setActived(false)
                ->setToken($this->_generateToken())
                ->setRoles([self::ROLE_USER])
                ->setTokenCreatedAt($currentTime)
                ->setAvatarUrl($randomAvatar)
                ->setCreatedAt($currentTime);
        }
    }

    /**
     * Encode password of user
     *
     * @param User $user
     * @return void
     */
    public function encodePassword(User $user): void
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

    /**
     * Retrive redirect URL
     *
     * @param $url
     * @return mixed
     */
    private function retrieveRedirectUrl($url): mixed
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Must be set to true so that PHP follows any "Location:" header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $a = curl_exec($ch); // $a will contain all headers
        return curl_getinfo($ch, CURLINFO_EFFECTIVE_URL); // This is what you need, it will return you the last effective URL
    }
}