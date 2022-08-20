<?php

namespace App\DataFixtures;

use App\Entity\Figure;
use App\Entity\Group;
use App\Entity\User;
use App\Managers\FigureManager;
use App\Managers\GroupManager;
use App\Managers\UserManager;
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

    private UserManager $_userManager;
    private FigureManager $_figureManager;
    private GroupManager $_groupManager;

    /**
     * Injections of require dependencies
     *
     * @param UserManager $userManager
     * @param FigureManager $figureManager
     * @param GroupManager $groupManager
     */
    public function __construct(UserManager $userManager, FigureManager $figureManager, GroupManager $groupManager)
    {
        $this->_userManager = $userManager;
        $this->_figureManager = $figureManager;
        $this->_groupManager = $groupManager;
    }

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
            $user = new User();
            $user
                ->setUsername($this->_faker->userName)
                ->setEmail($this->_faker->email)
                ->setPassword($this->_faker->password);
            $this->_userManager->save($user);
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
            $this->_groupManager->save($group);
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
                $figure = new Figure();
                $figure
                    ->setDescription($this->_faker->realText())
                    ->setImageUrl($this->_faker->imageUrl())
                    ->setName($this->_faker->name)
                    ->setGroup($group);
                $this->_figureManager->save($figure);
            }
        }
    }
}
