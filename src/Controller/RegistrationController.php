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
use Symfony\Component\Security\Guard\GuardAuthenticatorHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function register(LoginFormAuthenticator $authenticator, GuardAuthenticatorHandler $guardHandler, Request $request, UserPasswordEncoderInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && !$form->isValid()) {
            throw new BadRequestHttpException('Invalid data');
        }
        // si à la soumission du formulaire l'email est déjà utilisé, on redirige vers la page de connexion et on affiche un message d'erreur
        // Erreur 500 si l'email est déjà utilisé

        // $errors = $form->getErrors(true);
        // foreach ($errors as $error) {
        //     if ($error->getMessageTemplate() == 'This value is already used.') {
        //         $this->addFlash('error', 'Cette adresse email est déjà utilisée.');
        //         return $this->redirectToRoute('app_login');
        //     }
        // }


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

            try {
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

            return $guardHandler->authenticateUserAndHandleSuccess(
                $user,
                $request,
                $authenticator,
                'main' // firewall name in security.yaml
            );
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de l\'enregistrement. Votre compte est déjà utilisé.');
                return $this->redirectToRoute('app_login');
            }

        }
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
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
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $exception->getReason());

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');
        return $this->redirectToRoute('app_login');
    }
}
