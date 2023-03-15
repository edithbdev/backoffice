<?php

namespace App\Controller;

use App\Service\SendMailService;
use App\Repository\UserRepository;
use App\Form\ResetPasswordType;
use App\Form\VerifEmailRequestType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\ResetPasswordRequestType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authenticationUtils, Session $session): Response
    {
        // On récupère l'erreur s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entrer par l'utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();

        $session->start(); // Début de la session
        $session->set('lastUsername', $lastUsername); // On stocke le dernier username dans la session
        $session->set('error', $error); // On stocke l'erreur dans la session

        if ($this->getUser()) {
            return $this->redirectToRoute('homepage');
        }

        if ($session->get('lastUsername') == null) {
            $this->addFlash('info', 'Merci de vous connecter');
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(Session $session): ?Response
    {
        $session->invalidate(); // On invalide la session

        throw new \LogicException(
            'This method can be blank - it will be intercepted by the logout key on your firewall.'
        );
    }

    #[Route(path: '/forgotten-password', name: 'forgotten_password_request', methods: ['GET', 'POST'])]
    public function forgottenPasswordRequest(
        Request $request,
        UserRepository $usersRepository,
        TokenGeneratorInterface $tokenGenerator,
        EntityManagerInterface $em,
        SendMailService $mail
    ): Response {
        $form = $this->createForm(ResetPasswordRequestType::class);

        $form->handleRequest($request);

        $data = [];

        if ($form->isSubmitted() && !$form->isValid()) {
            $errors = $form->getErrors(true, true);
            foreach ($errors as $error) {
                if ($error instanceof FormError) {
                    $data[] = $error->getMessage();
                }
            }
            return $this->json($data, 400);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // On récupère l'utilisateur correspondant à l'email
            $user = $usersRepository->findOneByEmail(
                $form->get('email')->getData()
            );

            // On vérifie si l'utilisateur existe
            if ($user) {
                // on génère un token
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $em->persist($user);
                $em->flush();

                // on génère l'url de réinitialisation de mot de passe
                $url = $this->generateUrl(
                    'reset_password',
                    ['token' => $token],
                    UrlGeneratorInterface::ABSOLUTE_URL
                );

                // on crée un mail
                $mail->sendMail(
                    'no-reply@backoffice.edithbredon.fr',
                    (string)$user->getEmail(),
                    'Changement de mot de passe',
                    'password_reset',
                    ['user' => $user, 'url' => $url]
                );
                $this->addFlash(
                    'success',
                    'Un email de réinitialisation de mot de passe vous a été envoyé'
                );
            } else {
                $this->addFlash(
                    'danger',
                    'Aucun compte n\'est associé à cette adresse email'
                );
            }
        }
        return $this->render('security/reset_password_request.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/forgotten-password/{token}', name: 'reset_password', methods: ['GET', 'POST'])]
    public function resetPassword(
        string $token,
        Request $request,
        UserRepository $usersRepository,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        // On vérifie si le token est valide
        $user = $usersRepository->findOneByResetToken($token);

        if ($user) {
            $form = $this->createForm(ResetPasswordType::class);

            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // On supprime le token
                $user->setResetToken('');
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'Votre mot de passe a bien été modifié, retourner à la page de connexion' .
                    '<a href="' . $this->generateUrl('app_login') . '"> Se connecter</a>');
            }

            return $this->render('security/reset_password.html.twig', [
                'form' => $form->createView()
            ]);
        }
        $this->addFlash('danger', 'Un problème est survenu');
        return $this->redirectToRoute('app_login');
    }

    #[Route(path: '/send-verification-email', name: 'send_verification_email', methods: ['GET', 'POST'])]
    public function sendVerificationEmail(
        Request $request,
        UserRepository $usersRepository,
        TokenGeneratorInterface $tokenGenerator,
        EntityManagerInterface $em,
        SendMailService $mail
    ): Response {
        $form = $this->createForm(VerifEmailRequestType::class);

        $form->handleRequest($request);

        $data = [];

        if ($form->isSubmitted() && !$form->isValid()) {
            $errors = $form->getErrors(true, true);
            foreach ($errors as $error) {
                if ($error instanceof FormError) {
                    $data[] = $error->getMessage();
                }
            }
            return $this->json($data, 400);
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $usersRepository->findOneByEmail(
                $form->get('email')->getData()
            );

            if ($user) {
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $em->persist($user);
                $em->flush();

                $url = $this->generateUrl(
                    'verify_email',
                    ['token' => $token],
                    UrlGeneratorInterface::ABSOLUTE_URL
                );

                $mail->sendMail(
                    'no-reply@backoffice.edithbredon.fr',
                    (string)$user->getEmail(),
                    'Vérification de votre adresse email',
                    'email_verification',
                    ['user' => $user, 'url' => $url]
                );
                $this->addFlash(
                    'success',
                    'Un email de vérification de votre adresse email vous a été envoyé'
                );
            } else {
                $this->addFlash(
                    'danger',
                    'Aucun compte n\'est associé à cette adresse email'
                );
            }
        }
        return $this->render('security/verif_email_request.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/verify-email/{token}', name: 'verify_email', methods: ['GET', 'POST'])]
    public function verifyEmail(
        string $token,
        UserRepository $usersRepository,
        EntityManagerInterface $em
    ): Response {
        // On vérifie si le token est valide
        $user = $usersRepository->findOneByResetToken($token);

        if ($user) {
            $user->setIsVerified(true);
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Votre adresse email a bien été vérifiée');
        }

        return $this->redirectToRoute('app_login');
    }
}
