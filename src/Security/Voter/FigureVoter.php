<?php

namespace App\Security\Voter;

use App\Entity\Figure;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class FigureVoter extends Voter
{
    public const EDIT = 'FIGURE_EDIT';
    public const DELETE = 'FIGURE_DELETE';

    private Security $security;

    /**
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Check support
     *
     * @param string $attribute
     * @param $subject
     * @return bool
     */
    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::DELETE])
            && $subject instanceof Figure;
    }

    /**
     * @param string $attribute
     * @param Figure $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        // Authorise admin user
        if ($this->security->isGranted('ROLE_ADMIN')) return true;
        // Check if figure has author
        if (!$subject->getAuthor()) return false;
        return match ($attribute) {
            self::EDIT => $this->canEdit($user, $subject),
            self::DELETE => $this->canDelete($user, $subject),
            default => false,
        };
    }

    /**
     * @param UserInterface $user
     * @param Figure $figure
     * @return bool
     */
    public function canDelete(UserInterface $user, Figure $figure): bool
    {
        return $figure->getAuthor()->getId() == $user->getId();
    }


    /**
     * @param UserInterface $user
     * @param Figure $figure
     * @return bool
     */
    public function canEdit(UserInterface $user, Figure $figure): bool
    {
        return $figure->getAuthor()->getId() == $user->getId();
    }
}
