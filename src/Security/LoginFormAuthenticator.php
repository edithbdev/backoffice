<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\PasswordAuthenticatedInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\PasswordUpgradeBadge;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;

class LoginFormAuthenticator extends AbstractFormLoginAuthenticator implements PasswordAuthenticatedInterface
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    private $entityManager;
    private $urlGenerator;
    private $csrfTokenManager;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UrlGeneratorInterface $urlGenerator,
    CsrfTokenManagerInterface $csrfTokenManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->urlGenerator = $urlGenerator;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    public function supports(Request $request): bool
    {
        return self::LOGIN_ROUTE === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    public function getCredentials(Request $request)
    {
        $credentials = [
            'email' => $request->request->get('email'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['email']
        );

        return $credentials;
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?User
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);
        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException();
        }

        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $credentials['email']]);

        if (!$user) {
            // fail authentication with a custom error
            throw new CustomUserMessageAuthenticationException('Bad credentials.');
        }

        return $user;
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
    }

     /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function getPassword($credentials): ?string
    {
        return $credentials['password'];
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): ?RedirectResponse
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        // For example : return new RedirectResponse($this->urlGenerator->generate('some_route'));
        return new RedirectResponse($this->urlGenerator->generate('home'));

        // check if user has verified his email
        $verified = $this->entityManager->getRepository(User::class)->findOneBy(['isVerified' => true]);

        // Get user email address
        $email = $this->getCredentials($request)['email'];

        if ($verified) {
             return new RedirectResponse($this->urlGenerator->generate(self::LOGIN_ROUTE));
        } else {
            return new Response ('<html><body>Your email '.$email['email'].' is not verified. Please check your email for the verification link.</body></html>');
        }

        // For example : return new RedirectResponse($this->urlGenerator->generate('some_route'));
        // throw new \Exception('TODO: provide a valid redirect inside '.__FILE__);
        // return new RedirectResponse($this->urlGenerator->generate('home'));
    }

    protected function getLoginUrl(): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }

    public function authenticate(Request $request): Passport
    {
        $credentials = $this->getCredentials($request);

        $user = $this->getUser($credentials, $this->getUserProvider());

        $passport = new Passport(
            new UserBadge($user),
            new PasswordCredentials($credentials['password']),
            new CsrfTokenBadge($credentials['csrf_token']),
            new CustomCredentials($this->getPassword($credentials))
        );

        if (!$passport->isValid()) {
            throw new AuthenticationException('The presented passport is invalid.');
        }

        return $passport;
    }
    // {
    //     $password = $request->request->get('password', '');
    //     $email = $request->request->get('email', '');
    //     $csrfToken = $request->request->get('csrf_token');

    //     $request->getSession()->set(Security::LAST_USERNAME, $email);

        // ... get the $user from the $username and validate no
        // parameter is empty

        // return new Passport($username, new PasswordCredentials($password), [
        //     // $this->userRepository must implement PasswordUpgraderInterface
        //     new PasswordUpgradeBadge($password, $this->userRepository),
        //     new CsrfTokenBadge('login', $csrfToken),
        // ]);

        //  return $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
        //     }),
        //     [
        //         new CsrfTokenBadge('app_login', $csrfToken),
        //     ]
        // );


//         return new Passport(
//             new UserBadge($email),
//             new PasswordCredentials($password),
//             [
//                 new CsrfTokenBadge('app_login', $csrfToken),
//             ]
//         );
// }

    // public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    // {
    //     $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);

    //     return new RedirectResponse($this->urlGenerator->generate(self::LOGIN_ROUTE));
    // }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?RedirectResponse
    {
        $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);

        return new RedirectResponse($this->urlGenerator->generate(self::LOGIN_ROUTE));
    }
    // {
    //     $request->getSession()->set(Security::AUTHENTICATION_ERROR, $exception);

    //     $data = [
    //         // you may want to customize or obfuscate the message first
    //         'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

    //         // or to translate this message
    //         // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
    //     ];

    //     // Afficher le message et rediriger sur la page de login

    //     return





        //  return new RedirectResponse(
        //     $this->router->generate('app_login')
        // );

        // return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);




}
