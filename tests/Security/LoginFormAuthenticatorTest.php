<?php

namespace App\Tests\Security;

use PHPUnit\Framework\TestCase;
use App\Security\LoginFormAuthenticator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class LoginFormAuthenticatorTest extends TestCase
{
    public function testLoginFormAuthenticator(): void
    {
        $urlGenerator = $this->createMock(UrlGeneratorInterface::class);
        $urlGenerator->method('generate')->willReturn('homepage');
        $authenticator = new LoginFormAuthenticator($urlGenerator);
        $request = new Request();
        $request->setMethod('POST');
        $request->request->set('email', 'noemie@stinguette.fr');
        $request->request->set('password', 'admin123');
        $request->request->set('_csrf_token', 'authenticate');
        $request->attributes->set('_route', 'app_login');
        $request->setSession($this->createMock(SessionInterface::class));
        $this->assertTrue($authenticator->supports($request));

        $passport = $authenticator->authenticate($request);

        $this->assertInstanceOf(Passport::class, $passport);

        $this->assertEquals('noemie@stinguette.fr', $passport->getBadge(UserBadge::class)->getUserIdentifier());
        $this->assertEquals('admin123', $passport->getBadge(PasswordCredentials::class)->getPassword());
        $this->assertEquals('authenticate', $passport->getBadge(CsrfTokenBadge::class)->getCsrfToken());

        $this->assertInstanceOf(RememberMeBadge::class, $passport->getBadge(RememberMeBadge::class));

        $token = $this->createMock(TokenInterface::class);
        $response = $authenticator->onAuthenticationSuccess($request, $token, 'main');
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('homepage', $response->getTargetUrl());

        $response = $authenticator->onAuthenticationFailure($request, new AuthenticationException());
        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals('homepage', $response->getTargetUrl());

        $response = $authenticator->start($request);
    }
}
