<?php

namespace App\Controller\Admin\Users;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route('/admin/users', name: 'admin_users_')]
class LoginUsersController extends AbstractController
{
    #[Route('/login/{id}', name: 'login', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function loginUser(
        Request $request,
        UserPasswordHasherInterface $passwordEncoder,
        UserRepository $userRepository,
        TokenStorageInterface $tokenStorage,
        SessionInterface $session,
        int $id
    ): Response {
        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('Access denied');
        }

        // Déconnecter l'utilisateur actuel s'il en existe un
        if ($this->getUser()) {
            // on déconnecte l'utilisateur actuel en supprimant le token
            $tokenStorage->setToken(null);
            // on supprime la session
            $session->invalidate();
        }

        // Connecter le nouvel utilisateur
        // On génère un token pour l'utilisateur et on le stocke dans la session
        $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
        $tokenStorage->setToken($token);
        // en session, on stocke le token sous la clé _security_main
        $session->set('_security_main', serialize($token));

        return $this->redirectToRoute('homepage');
    }
}
