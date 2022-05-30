<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @param Session $session
     * @return Response
     */
    public function login(
        AuthenticationUtils $authenticationUtils,
        Session $session
    ): Response {
        // If already logged in, redirect to home page
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        // Get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // Last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        if ($session->has('message')) {
            $message = $session->get('message');
            $session->remove('message'); // we remove the message from the session
            $return['message'] = $message; // we add the message to the array of parameters
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

//    /**
//     * @Route("/verify/email", name="app_verify_email")
//     */
//     public function verifyUserEmail(Request $request): Response
//     {
//         $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

//         // validate email confirmation link, sets User::isVerified=true and persiststry {$this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
//         try {
//             $this->get('app.email_verifier')->handleEmailConfirmation($request, $this->getUser());
//         } catch (VerifyEmailExceptionInterface $exception) {
//             $this->addFlash('verify_email_error', $exception->getReason());

//             return $this->redirectToRoute('register');
//         }

//         $user = $this->getUser();
//         $user->setIsVerified(true);
//         $this->getDoctrine()->getManager()->flush();

//         $this->addFlash('success', 'Your email address has been verified.');

//         return $this->redirectToRoute('home');
//     }

    // /**
    //  * @Route("/register", name="register", methods={"GET", "POST"})
    //  * @param Request $request
    //  * @param EntityManagerInterface $entityManager
    //  * @param UserPasswordEncoderInterface $passwordEncoder
    //  * @param VerifyEmailHelperInterface $verifyEmailHelper
    //  * @return Response
    //  */
    // public function register(
    //     Request $request,
    //     EntityManagerInterface $entityManager,
    //     UserPasswordEncoderInterface $passwordEncoder,
    //     VerifyEmailHelperInterface $verifyEmailHelper
    // ): Response {
    //     // Create a new user
    //     $user = new User();
    //     $form = $this->createForm(UserType::class, $user);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         // Encode the plain password
    //         $user->setPassword(
    //             $passwordEncoder->encodePassword(
    //                 $user,
    //                 $form->get('plainPassword')->getData()
    //             )
    //         );

    //         $user->setRoles(['ROLE_USER']);
    //         $user->setApiToken(bin2hex(random_bytes(60)));

    //         // Set the user as not verified
    //         $user->setIsVerified(false);

    //         // Generate a unique token for email verification
    //         $token = $verifyEmailHelper->generateToken();
    //         $user->setVerifyToken($token);

    //         // Save the user
    //         $entityManager->persist($user);
    //         $entityManager->flush();

    //         // Send email verification
    //         $verifyEmailHelper->sendEmailConfirmation($user);

    //         // Flash message
    //         $this->addFlash('success', 'We have sent you an email. Please click on the link to verify your email address.');

    //         return $this->redirectToRoute('home');
    //     }

    //     return $this->render('security/register.html.twig', [
    //         'form' => $form->createView(),
    //     ]);
    // }



    /**
     * @Route("/profile/{user}", name="profile", methods={"GET", "POST"})
     * @ParamConverter("user", options={"mapping": {"user": "username"}})
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param Session $session
     * @return Response
     */
    public function profile(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        Session $session,
        UserRepository $userRepository
    ): Response {
        // We get the user who is connected
        $user = $userRepository->findOneBy(['id' => $this->getUser()->getId()]);

        // If the user is not connected, he can't access to the profile page
        if (
            $user->getId() != $this->getUser()->getId() &&
            $user->getUsername() != $this->getUser()->getUsername()
        ) {
            $session->set(
                'message',
                'Vous ne pouvez pas modifier cet utilisateur'
            );
            return $this->redirectToRoute('home');
        }

        // Update the user profile
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte a bien été modifié.');

            return $this->redirectToRoute('home');
        }

        return $this->render('security/profil.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException(
            'This method can be blank - it will be intercepted by the logout key on your firewall.'
        );
    }
}
