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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * @param AuthenticationUtils $authenticationUtils
     * @param Session $session
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils, Session $session): Response
    {
       // If already logged in, redirect to home page
        if ($this->getUser()) {
            return $this->redirectToRoute('target_path');
        }

        // Get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // Last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        if($session->has('message'))
            {
                    $message = $session->get('message');
                    $session->remove('message'); // we remove the message from the session
                    $return['message'] = $message; // we add the message to the array of parameters
            }

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/register", name="register", methods={"GET", "POST"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param Session $session
     * @return Response
     */
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, Session $session): Response
    {
        // Security test, if the user is not connected, he can't access to the register page
        // $user = $this->getUser();
        // if($user)
        // {
        //         $session->set("message", "Vous ne pouvez pas créer un compte lorsque vous êtes connecté");
        //          return $this->redirectToRoute('home');
        // }
        // si le formulaire contient des errors, on les récupère
        // if($session->has('errors'))
        // {
        //     $errors = $session->get('errors');
        //     $session->remove('errors');
        // }
        // else
        // {
        //     $errors = [];
        // }


        // Create a new user
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
             $newPassword = $form->get('password')->getData();
            if ($newPassword !=null) {
               $encodedPassword = $passwordEncoder->encodePassword($user, $newPassword);
               $user->setPassword($encodedPassword);
            }
            $user->setApiToken(md5(uniqid()));
            $user->setRoles(['ROLE_USER']);
            $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

              $this->addFlash('success', 'Votre compte a bien été créé, vous pouvez vous connecter avec vos identifiants');

            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('security/register.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

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
    public function profile(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder, Session $session, UserRepository $userRepository): Response
    {
        // We get the user who is connected
        $user = $userRepository->findOneBy(['id' => $this->getUser()->getId()]);

        // If the user is not connected, he can't access to the profile page
        if ($user->getId() != $this->getUser()->getId() && $user->getUsername() != $this->getUser()->getUsername())
        {
            $session->set("message", "Vous ne pouvez pas modifier cet utilisateur");
            return $this->redirectToRoute('home');
        }

        // Update the user profile
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $user->setUpdatedAt(new \DateTimeImmutable());
            $entityManager->persist($user);
            $entityManager->flush();

        $this->addFlash(
                'success',
                'Votre compte a bien été modifié.'
            );

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
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
