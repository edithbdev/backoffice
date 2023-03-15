<?php

namespace App\Controller\Admin\Users;

use App\Repository\UserRepository;
use Symfony\Contracts\Cache\ItemInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/users', name: 'admin_users_')]
class IndexUsersController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(
        UserRepository $users,
        PaginatorInterface $paginator,
        Request $request,
        CacheInterface $cache,
    ): Response {

        $cachedUsers = $cache->get('users_list', function (ItemInterface $item) use ($users) {
            $item->expiresAfter(3600);
            return $users->findAllUsers();
        });

        $pagination = $paginator->paginate(
            $cachedUsers,
            $request->query->getInt('page', 1),
            10,
        );

        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'Vous n\'avez pas les droits pour accéder à cette page');
            return $this->redirectToRoute('home');
        }

        return $this->render('admin/users/index.html.twig', [
            'users' => $pagination,
            'entity' => 'users',
        ]);
    }
}
