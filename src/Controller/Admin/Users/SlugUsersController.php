<?php

namespace App\Controller\Admin\Users;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/users', name: 'admin_users_')]
class SlugUsersController extends AbstractController
{
    // {} indicates that it is variable in the url
    #[Route('/{slug}', name: 'slug', requirements: ['slug' => '[a-z0-9\-]+'], methods: ['GET'])]
    public function slug(
        UserRepository $user,
        string $slug
    ): Response {
        $users = $user->findOneBy(['slug' => $slug, 'deleted' => false]);

        if (!$users) {
            throw $this->createNotFoundException('User not found');
        }

        return $this->render('admin/users/slug.html.twig', [
            'user' => $users,
            'entity' => 'users',
        ]);
    }
}
