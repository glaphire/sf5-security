<?php

namespace App\Security;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;

class LoginFormAuthenticator extends AbstractAuthenticator
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function supports(Request $request): ?bool
    {
        return $request->getPathInfo() === '/login' && $request->isMethod('POST');
    }

    public function authenticate(Request $request): PassportInterface
    {
        $email = $request->request->get('email', function($userIdentifier) {
            $user = $this->userRepository->findOneBy(['email' => $userIdentifier]);

            if (!$user) {
                throw new UserNotFoundException();
            }

            return $user;
        });

        $password = $request->request->get('password');
        return new Passport(
            new UserBadge($email),
            new CustomCredentials(function($credentials, User $user) {
                return $credentials === 'tada';
            }, $password)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        dd('success');
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        dd('failure');
    }

//    public function start(Request $request, AuthenticationException $authException = null): Response
//    {
//        /*
//         * If you would like this class to control what happens when an anonymous user accesses a
//         * protected page (e.g. redirect to /login), uncomment this method and make this class
//         * implement Symfony\Component\Security\Http\EntryPoint\AuthenticationEntrypointInterface.
//         *
//         * For more details, see https://symfony.com/doc/current/security/experimental_authenticators.html#configuring-the-authentication-entry-point
//         */
//    }
}
