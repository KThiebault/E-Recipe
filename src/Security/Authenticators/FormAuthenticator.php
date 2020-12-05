<?php

namespace App\Security\Authenticators;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\PasswordUpgradeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;

class FormAuthenticator extends AbstractAuthenticator
{
    private UserRepository $userRepository;
    private UrlGeneratorInterface $urlGenerator;

    private const CSRF_TOKEN = "security_login_form";
    private const LOGIN_ROUTE = "security_login";
    private const SUCCESS_ROUTE = "account_index";

    public function __construct(UserRepository $userRepository, UrlGeneratorInterface $urlGenerator)
    {
        $this->userRepository = $userRepository;
        $this->urlGenerator = $urlGenerator;
    }

    public function supports(Request $request): ?bool
    {
        return $request->isMethod(Request::METHOD_POST) && $request->get("_route") === self::LOGIN_ROUTE;
    }

    public function authenticate(Request $request): PassportInterface
    {
        return new Passport(
            new UserBadge($request->request->get("email")),
            new PasswordCredentials($request->request->get("password")),
            [
                new CsrfTokenBadge(self::CSRF_TOKEN, $request->request->get("csrf")),
                new PasswordUpgradeBadge($request->request->get("password"), $this->userRepository)
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return new RedirectResponse($this->urlGenerator->generate(self::SUCCESS_ROUTE));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        if ($request->hasSession()) {
            $request->getSession()->set(Security::AUTHENTICATION_ERROR, "Invalid credentials");
        }
        return new RedirectResponse($this->urlGenerator->generate(self::LOGIN_ROUTE));
    }
}
