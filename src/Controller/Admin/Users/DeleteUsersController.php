<?php

namespace App\Controller\Admin\Users;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/users', name: 'admin_users_')]
class DeleteUsersController extends AbstractController
{
    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        EntityManagerInterface $em,
        UserRepository $user,
        Session $session,
        string $id,
        CacheInterface $cache
    ): Response {
        $userToDelete = $user->findOneBy(['id' => $id]);
        if ($this->isCsrfTokenValid('delete' . $id, (string)$request->request->get('_token'))) {
            if ($request->request->get('_token') !== null) {
                if ($userToDelete !== null) {
                    $userToDelete->setDeleted(true);
                    $em->persist($userToDelete);
                    $em->flush();
                    $cache->delete('users_list');
                    $session->invalidate();
                    $this->addFlash('success', 'User ' . $userToDelete->getFirstname() . ' has been deleted');
                    return $this->redirectToRoute('admin_users_index');
                } else {
                    $this->addFlash('danger', 'User not found');
                    return $this->redirectToRoute('admin_users_index');
                }
            }
        }
        return $this->redirectToRoute('admin_users_index');
    }
}
