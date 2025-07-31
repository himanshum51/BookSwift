<?php

namespace App\Security\Voter;

use App\Entity\Event;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class EventVoter extends Voter
{
    public const MANAGE = 'MANAGE';

    protected function supports(string $attribute, $subject): bool
    {
        return $attribute === self::MANAGE && $subject instanceof Event;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Event $event */
        $event = $subject;

        return match ($attribute) {
            self::MANAGE => $this->canManage($event, $user),
            default => false,
        };
    }

    private function canManage(Event $event, User $user): bool
    {
        return $event->getCreatedBy() === $user;
    }
}
