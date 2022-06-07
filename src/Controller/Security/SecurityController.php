<?php

namespace App\Controller\Security;

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
    public function login(
        AuthenticationUtils $authenticationUtils,
        Session $session
    ): Response {
        // If already logged in, redirect to home page
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        // if user had reset password and is now logging in, redirect to home page
        if ($session->has('reset_password')) {
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

    /**
     * @Route("/profil/{user}", name="profil", methods={"GET", "POST"})
     * @ParamConverter("user", options={"mapping": {"user": "username"}})
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param Session $session
     * @return Response
     */
    public function profil(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        Session $session,
        UserRepository $userRepository
    ): Response {
        // We get the user who is connected
        $user = $userRepository->findOneBy(['id' => $this->getUser()->getId()]);

        // If the user is not connected, he can't access to the profil page
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

        // Update the user profil
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

    // route pour mettre en statut isDeleted à true pour supprimer le compte
    /**
     * @Route("/profil/delete/{user}", name="delete_user", methods={"GET", "POST"})
     * @ParamConverter("user", options={"mapping": {"user": "username"}})
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param Session $session
     * @return Response
     */

    public function delete(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        Session $session,
        UserRepository $userRepository
    ): Response {
        // We get the user who is connected
        $user = $userRepository->findOneBy(['id' => $this->getUser()->getId()]);

        // If the user is not connected, he can't access to the profil page
        if (
            $user->getId() != $this->getUser()->getId() &&
            $user->getUsername() != $this->getUser()->getUsername()
        ) {
            $session->set(
                'message',
                'Vous ne pouvez pas supprimer cet utilisateur'
            );
            return $this->redirectToRoute('home');
        }

        // Delete the user account
        $user = $userRepository->findOneBy(['isDeleted' => false]);
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setIsDeleted(true);
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte a bien été supprimé.');

            return $this->redirectToRoute('app_logout');
        }
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
