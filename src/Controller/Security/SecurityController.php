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
     * @Route("/profil/edit/{id}", name="user_profil_update", methods={"GET", "POST"}), requirements={"id"="\d+"})
     * @ParamConverter("id", class="App\Entity\User", options={"id"="id"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @return Response
     */
    public function EditProfil(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        User $user
    ): Response {
        // We get the user who is connected
        // $user = $userRepository->findOneBy(['id' => $this->getUser()->getId()]);

         $this->denyAccessUnlessGranted('ROLE_USER', $user->getId());

        //If the profil is not the connected user, redirect to home page
        // if ($user->getId() != $this->getUser()->getId()) {
        //     $session->set('message', 'Vous n\'avez pas accès à cette page');
        //     return $this->redirectToRoute('home');
        // }


        // Update the user profil
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

          if ($form->isSubmitted() && !$form->isValid()) {
            $this->addFlash('error', 'Please check your input');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // If the user has changed his password, and it is different from the default password
            $newPassword = $form->get('plainPassword')->getData();

            if ($newPassword != null) {
                $encodedPassword = $passwordEncoder->encodePassword(
                    $user,
                    $newPassword
                );
                $user->setPassword($encodedPassword);
            }

            $user->setUpdatedAt(new \DateTimeImmutable());

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'successUpdate',
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
     * @Route("/profil/delete/{id}", name="user_profil_delete", methods={"DELETE", "POST"}, requirements={"id"="\d+"})
     * @ParamConverter("id", class="App\Entity\User", options={"id"="id"})
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param Session $session
     * @return Response
     */
    public function delete(
        Request $request,
        EntityManagerInterface $entityManager,
        Session $session,
        UserRepository $userRepository
    ): Response {
        // if user is the user who is connected, he can delete his account
        $user = $userRepository->findOneBy(['id' => $this->getUser()->getId()]);

        if ($user->getId() != $this->getUser()->getId()) {
            $session->set(
                'message',
                'Vous ne pouvez pas supprimer cet utilisateur'
            );
            return $this->redirectToRoute('home');
        }

        if (
            $this->isCsrfTokenValid(
                'delete' . $user->getId(),
                $request->request->get('_token')
            )
        ) {
            $entityManager->setIsDeleted($user, true);
            $entityManager->flush();

            $this->addFlash('success', 'Votre compte a bien été supprimé.');

            $session->invalidate();
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
