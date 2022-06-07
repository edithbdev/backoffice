<?php

namespace App\Controller;

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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

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
    public function register(Request $request, UserPasswordEncoderInterface $userPasswordHasher, UserAuthenticatorInterface $authenticator, LoginFormAuthenticator $formAuthenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->encodePassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setRoles(['ROLE_USER']);
            $user->setApiToken(bin2hex(random_bytes(60)));

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('contact@edithbredon.fr', 'no-reply@edithbredon.fr'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email
             $this->addFlash('success', 'Confirm your email at: ' . $user->getEmail());

            return $this->redirectToRoute('app_login');

            // $authenticator->authenticateUser($user, $formAuthenticator, $request);

            // return $userAuthenticator->authenticateUser(
            //     $user,
            //     $request,
            //     $authenticator,
            //     'main'
            // );

            // return new Response(
            //     'Check your mailbox and click on the link to verify your email address.',
            //     Response::HTTP_CREATED
            //     return $this->redirectToRoute('app_login');
            // );

            // Argument 2 passed to Symfony\Bundle\SecurityBundle\Security\UserAuthenticator::authenticateUser() must implement interface Symfony\Component\Security\Http\Authenticator\AuthenticatorInterface, instance of Symfony\Component\HttpFoundation\Request given, called in D:\Documents\backoffice\src\Controller\RegistrationController.php on line 67
            // return $authenticator->authenticate($request, $userAuthenticator);

// return $userAuthenticator->authenticateUser(
//                 $user,
//                 $request,
//                 $authenticator
//             );



        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/verify/email/", name="app_verify_email")
     */
    public function verifyUserEmail(Request $request, TranslatorInterface $translator, User $user, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $id = $request->query->get('id');
        $token = $request->query->get('token');
        $email = $request->query->get('email');



        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $id, $token, $email);
            $user->setIsVerified(true);
            $entityManager->flush();
            return $this->redirectToRoute('app_login');
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_login');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified! You can now log in.');

        return $this->redirectToRoute('app_login');
    }
}
