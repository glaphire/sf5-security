<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Http\Authenticator\Passport\UserPassportInterface;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;

class CheckVerifiedUserSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            CheckPassportEvent::class => ['onCheckPassport', -10],
        ];
    }

    public function onCheckPassport(CheckPassportEvent $event)
    {
        $passport = $event->getPassport();

        if (!$passport instanceof UserPassportInterface) {
            throw new \Exception('Unknown passport type');
        }

        $user = $passport->getUser();

        if (!$user instanceof User) {
            throw new \Exception('Unknown user type');
        }

        if (!$user->getIsVerified()) {
            throw new CustomUserMessageAccountStatusException(
                'Please verify your account before logging in'
            );
        }
    }
}