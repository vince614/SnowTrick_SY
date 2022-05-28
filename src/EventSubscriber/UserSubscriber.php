<?php

namespace App\EventSubscriber;

use App\Entity\User;
use App\Event\RegistrationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserSubscriber implements EventSubscriberInterface
{
    /** @var MailerInterface  */
    protected $mailer;
    /** @var UrlGeneratorInterface  */
    protected $router;

    public function __construct(MailerInterface $mailer, UrlGeneratorInterface $router)
    {
        $this->mailer = $mailer;
        $this->router = $router;
    }


    /**
     * Get subscriber events
     *
     * @return string[]
     */
    public static function getSubscribedEvents(): array
    {
        return [
            RegistrationEvent::NAME => 'registerUser'
        ];
    }

    /**
     * Register user event
     *
     * @param RegistrationEvent $event
     * @return void
     */
    public function registerUser(RegistrationEvent $event)
    {
        $user = $event->getUser();
    }



}