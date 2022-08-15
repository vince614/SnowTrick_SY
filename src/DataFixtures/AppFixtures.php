<?php

namespace App\DataFixtures;

use App\Entity\Figure;
use App\Entity\Group;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    /** @var int */
    const USERS_COUNT   = 10;
    const FIGURES_COUNT = 20;
    const GROUPS        =  ['Frabs', 'Rotations', 'Flips', 'Rotations désaxées', 'Slides', 'One foot tricks', 'Old school'];

    /** @var ObjectManager  */
    private ObjectManager $_manager;

    /** @var Generator  */
    private Generator $_faker;

    /** @var array  */
    private array $_groups = [];

    /**
     * Load fixtures
     *
     * @throws Exception
     */
    public function load(ObjectManager $manager)
    {
        $this->_manager = $manager;
        $this->_faker = Factory::create('fr_FR');

        // Creates entities
        $this->_createUsers();
        $this->_createGroups();
        $this->_createFigures();

        // Flush
        $this->_manager->flush();
    }

    /**
     * Create user
     *
     * @return void
     */
    private function _createUsers(): void
    {
        for ($i = 0; $i < self::USERS_COUNT; $i++) {
            $dateTime = new DateTimeImmutable();
            $user = new User();
            $user
                ->setPassword($this->_faker->password)
                ->setUsername($this->_faker->userName)
                ->setCreatedAt($dateTime)
                ->setEmail($this->_faker->email)
                ->setActived($this->_faker->boolean())
                ->setToken($this->_faker->linuxPlatformToken)
                ->setRoles(["ROLE_USER"])
                ->setTokenCreatedAt($dateTime);
            $this->_manager->persist($user);
        }
    }

    /**
     * Create groups
     *
     * @return void
     */
    private function _createGroups(): void
    {
        foreach (self::GROUPS as $groupName) {
            $group = new Group();
            $group->setName($groupName);
            $this->_groups[] = $group;
            $this->_manager->persist($group);
        }
    }

    /**
     * Create figures
     *
     * @return void
     */
    private function _createFigures(): void
    {
        foreach ($this->_groups as $group) {
            for ($i = 0; $i < self::FIGURES_COUNT; $i++) {
                $dateTime = new DateTimeImmutable();
                $figure = new Figure();
                $figure
                    ->setCreatedAt($dateTime)
                    ->setDescription($this->_faker->text)
                    ->setImageUrl($this->_faker->imageUrl())
                    ->setName($this->_faker->name)
                    ->setSlug($figure->getName())
                    ->setGroup($group);
                $this->_manager->persist($figure);
            }
        }
    }
}
