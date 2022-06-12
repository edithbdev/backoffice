<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Security\EmailVerifier;
use App\Form\RegistrationFormType;
use Symfony\Component\Mime\Address;
use App\Security\LoginFormAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(
        LoginFormAuthenticator $authenticator,
        GuardAuthenticatorHandler $guardHandler,
        Request $request,
        UserPasswordEncoderInterface $userPasswordHasher,
        EntityManagerInterface $entityManager
    ): Response {
        $user = new User();
        // if email is already verified and user is already registered, log him in
        if (
            $user->getEmail() !== null &&
            $user->getEmailVerifiedAt() !== null
        ) {
            $this->addFlash('error', 'Your account is already in use');
            return $this->redirectToRoute('app_login');
        }

        $captcha = $request->request->get('g-recaptcha-response');

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        // if ($form->isSubmitted() && !$form->isValid()) {
        //      $captcha = $form->getData()['g-recaptcha-response'];
        //     if ($captcha === null) {
        //         $this->addFlash('error', 'Please check the reCAPTCHA');
        //         return $this->redirectToRoute('app_register');
        //     } else {
        //         $this->addFlash('error', 'Please check the form');
        //         return $this->redirectToRoute('app_register');
        //     }
        //     // throw new BadRequestHttpException('Invalid data');
        // }

        if ($form->isSubmitted() && $form->isValid() && $captcha) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(['ROLE_USER']);
            $user->setApiToken(bin2hex(random_bytes(60)));

            try {
                $entityManager->persist($user);
                $entityManager->flush();

                // generate a signed url and email it to the user
                $this->emailVerifier->sendEmailConfirmation(
                    'app_verify_email',
                    $user,
                    (new TemplatedEmail())
                        ->from(
                            new Address(
                                'contact@edithbredon.fr',
                                'no-reply@edithbredon.fr'
                            )
                        )
                        ->to($user->getEmail())
                        ->subject('Please Confirm your Email')
                        ->htmlTemplate(
                            'registration/confirmation_email.html.twig'
                        )
                );

                return $guardHandler->authenticateUserAndHandleSuccess(
                    $user,
                    $request,
                    $authenticator,
                    'main' // firewall name in security.yaml
                );
            } catch (\Exception $e) {
                // we check if recaptcha is valid
                if (!$captcha) {
                    $this->addFlash('error', 'Please check the reCAPTCHA');
                    return $this->redirectToRoute('app_register');
                }

                // si email déjà utilisé
                if ($e instanceof UniqueConstraintViolationException) {
                    $this->addFlash('error', 'Your account is already in use');
                    return $this->redirectToRoute('app_register');
                }
                // si email invalide
                if ($e instanceof VerifyEmailExceptionInterface) {
                    $this->addFlash('error', 'Your email is invalid');
                    return $this->redirectToRoute('app_register');
                }
                //  $this->addFlash(
                //     'error',
                //     'An error occurred while saving'
                // );
                // return $this->redirectToRoute('app_login');
            }
             // do anything else you need here, like send an email
                $this->addFlash(
                    'success',
                    'Confirm your email at: ' . $user->getEmail()
                );
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/registration/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verify/email/", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation(
                $request,
                $this->getUser()
            );
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');
        return $this->redirectToRoute('app_login');
    }
}
