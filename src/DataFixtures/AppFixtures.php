<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\Figure;
use App\Entity\Group;
use App\Entity\Image;
use App\Entity\User;
use App\Entity\Video;
use App\Managers\CommentManager;
use App\Managers\FigureManager;
use App\Managers\GroupManager;
use App\Managers\UserManager;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Faker\Factory;
use Faker\Generator;

class AppFixtures extends Fixture
{
    /**
     * User options
     */
    const USERS_MAX_COUNT   = 50;

    /**
     * Figures options
     */
    const FIGURES_MAX_COUNT = 5;
    const IMAGES_MAX_COUNT = 5;
    const VIDEOS_MAX_COUNT = 1;
    const COMMENTS_MAX_COUNT = 20;

    /**
     * Groups options
     */
    const GROUPS        =  ['Frabs', 'Rotations', 'Flips', 'Rotations désaxées', 'Slides', 'One foot tricks', 'Old school'];

    /** @var Generator  */
    private Generator $_faker;

    /** @var Group[]  */
    private array $_groups = [];

    /** @var array  */
    private array $_users = [];

    /** @var array|string[]  */
    private array $_logsFormats = [
        // italic and blink may not work depending of your terminal
        'bold' => "\033[1m%s\033[0m",
        'dark' => "\033[2m%s\033[0m",
        'italic' => "\033[3m%s\033[0m",
        'underline' => "\033[4m%s\033[0m",
        'blink' => "\033[5m%s\033[0m",
        'reverse' => "\033[7m%s\033[0m",
        'concealed' => "\033[8m%s\033[0m",
        // foreground colors
        'black' => "\033[30m%s\033[0m",
        'red' => "\033[31m%s\033[0m",
        'green' => "\033[32m%s\033[0m",
        'yellow' => "\033[33m%s\033[0m",
        'blue' => "\033[34m%s\033[0m",
        'magenta' => "\033[35m%s\033[0m",
        'cyan' => "\033[36m%s\033[0m",
        'white' => "\033[37m%s\033[0m",
        // background colors
        'bg_black' => "\033[40m%s\033[0m",
        'bg_red' => "\033[41m%s\033[0m",
        'bg_green' => "\033[42m%s\033[0m",
        'bg_yellow' => "\033[43m%s\033[0m",
        'bg_blue' => "\033[44m%s\033[0m",
        'bg_magenta' => "\033[45m%s\033[0m",
        'bg_cyan' => "\033[46m%s\033[0m",
        'bg_white' => "\033[47m%s\033[0m",
    ];

    private UserManager $_userManager;
    private FigureManager $_figureManager;
    private GroupManager $_groupManager;
    private CommentManager $_commentManager;

    private UserRepository $_userRepository;

    private ObjectManager $_manager;

    private int $_userCount = 0;
    private int $_figureCount = 0;
    private int $_commentCount = 0;
    private int $_groupCount = 0;

    /**
     * Injections of require dependencies
     *
     * @param UserManager $userManager
     * @param FigureManager $figureManager
     * @param GroupManager $groupManager
     * @param UserRepository $userRepository
     * @param CommentManager $commentManager
     */
    public function __construct(
        UserManager $userManager,
        FigureManager $figureManager,
        GroupManager $groupManager,
        UserRepository $userRepository,
        CommentManager $commentManager
    )
    {
        $this->_userManager = $userManager;
        $this->_figureManager = $figureManager;
        $this->_groupManager = $groupManager;
        $this->_userRepository = $userRepository;
        $this->_commentManager = $commentManager;
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
        $this->_createAdminUser();
        $this->_createUsers();
        $this->_createGroups();
        $this->_createFigures();

        $this->log(PHP_EOL . 'Fixtures was loaded');
        $this->log("$this->_userCount Users", 'green');
        $this->log("$this->_figureCount Figures", 'green');
        $this->log("$this->_commentCount Comments", 'green');
        $this->log("$this->_groupCount Groups", 'green');

        // Flush
        $this->_manager->flush();
    }

    /**
     * Create admin user
     *
     * @return void
     */
    private function _createAdminUser(): void
    {
        $currentTime = new DateTimeImmutable();
        $randomAvatar = $this->retrieveRedirectUrl('https://picsum.photos/300/300');
        $user = new User();
        $user
            ->setUsername("Vince")
            ->setEmail("vince@gmail.com")
            ->setPassword("4f6asLQ8BN!o9BpM")
            ->setTokenCreatedAt($currentTime)
            ->setCreatedAt($currentTime)
            ->setRoles([$this->_userManager::ROLE_ADMIN])
            ->setActived(true)
            ->setAvatarUrl($randomAvatar)
            ->setToken("token_admin");
        $this->log('Admin Vince was created', 'green');
        $this->_userManager->encodePassword($user);
        $this->_manager->persist($user);
        $this->_manager->flush();
        $this->_userCount++;
    }

    /**
     * Create user
     *
     * @return void
     * @throws Exception
     */
    private function _createUsers(): void
    {
        $userCount = random_int(25, self::USERS_MAX_COUNT);
        $this->log("Create $userCount users");
        for ($i = 0; $i < $userCount; $i++) {
            $user = new User();
            $user
                ->setUsername($this->_faker->userName)
                ->setEmail($this->_faker->email)
                ->setPassword($this->_faker->password);
            $this->log('User ' . $this->_faker->userName. ' was created', 'green');
            $this->_users[] = $user->getEmail();
            $this->_userManager->save($user);
            $this->_userCount++;
        }
    }

    /**
     * Create groups
     *
     * @return void
     */
    private function _createGroups(): void
    {
        $this->log('Create groups');
        foreach (self::GROUPS as $groupName) {
            $group = new Group();
            $group->setName($groupName);
            $this->_groups[] = $group;
            $this->log("Group $groupName was created", 'green');
            $this->_groupManager->save($group);
            $this->_groupCount++;
        }
    }

    /**
     * Create figures
     *
     * @return void
     * @throws Exception
     */
    private function _createFigures(): void
    {
        foreach ($this->_groups as $group) {
            $figureCount = random_int(1, self::FIGURES_MAX_COUNT);
            $this->log("Create $figureCount figures in group " . $group->getName());
            for ($x = 0; $x < $figureCount; $x++) {
                $figure = new Figure();
                $randomImageLink = $this->retrieveRedirectUrl('https://picsum.photos/500/300');
                $adminUser = $this->_userRepository->findOneBy(['email' => 'vince@gmail.com']);
                $figure
                    ->setDescription($this->_faker->realText(2000))
                    ->setImageUrl($randomImageLink)
                    ->setName($this->_faker->name)
                    ->setAuthor($adminUser)
                    ->setGroup($group);
                $this->log("Figure " . $this->_faker->name . " was created", 'green');

                $this->_figureManager->save($figure);
                $this->_figureCount++;

                // Create images
                $imagesCount = random_int(1, self::IMAGES_MAX_COUNT);
                $this->log("Create $imagesCount images", 'yellow');
                for ($i = 0; $i < $imagesCount; $i++) {
                    $image = new Image();
                    $randomImageLink = $this->retrieveRedirectUrl('https://picsum.photos/500/300');
                    $image
                        ->setName($this->_faker->name)
                        ->setUrl($randomImageLink)
                        ->setFigure($figure);
                    $this->_manager->persist($image);
                    $this->_manager->flush();
                    $figure->addImage($image);
                }

                // Create videos
                $videoCount = random_int(1, self::VIDEOS_MAX_COUNT);
                $this->log("Create $videoCount videos", 'yellow');
                for ($i = 0; $i < $videoCount; $i++) {
                    $video = new Video();
                    $video
                        ->setName($this->_faker->name)
                        ->setUrl('https://www.youtube.com/embed/tgbNymZ7vqY')
                        ->setFigure($figure);
                    $this->_manager->persist($video);
                    $this->_manager->flush();
                    $figure->addVideo($video);
                }

                // Create comments
                $commentCount = random_int(2, self::COMMENTS_MAX_COUNT);
                $this->log("Create $commentCount comments", 'yellow');
                foreach ($this->getRandomUserEmails($commentCount) as $email) {
                    $user = $this->_userRepository->findOneBy(['email' => $email]);
                    $comment = new Comment();
                    $comment
                        ->setUser($user)
                        ->setComment($this->_faker->realText(500));
                    $this->_commentManager->save($comment);
                    $figure->addComment($comment);
                    $this->_commentCount++;
                }
                $this->_figureManager->save($figure);
            }
        }
    }

    /**
     * Get random user email
     *
     * @param $userCount
     * @return array
     */
    private function getRandomUserEmails($userCount): array
    {
        $result = [];
        $randomIndexs = array_rand($this->_users, $userCount);
        foreach ($randomIndexs as $index) $result[] = $this->_users[$index];
        return $result;
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

    /**
     * Log
     *
     * @param $msg
     * @param $format
     * @return void
     */
    private function log($msg, $format = null): void
    {
        if ($format) $msg = $this->setLogFormat($format, $msg);
        echo $msg . PHP_EOL;
    }

    /**
     * Set logs format
     *
     * @param $format
     * @param $msg
     * @return string
     */
    private function setLogFormat($format, $msg): string
    {
        return sprintf($this->_logsFormats[$format], $msg);
    }
}
