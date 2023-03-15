<?php

namespace App\Controller\Admin\Tools;

use App\Repository\ToolRepository;
use Psr\Cache\CacheItemPoolInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/tools', name: 'admin_tools_')]
class DeleteToolsController extends AbstractController
{
    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function delete(
        EntityManagerInterface $em,
        Request $request,
        ToolRepository $tools,
        string $id,
        CacheInterface $cache,
        CacheItemPoolInterface $cacheItemPool
    ): Response {
        $toolToDelete = $tools->findOneBy(
            ['id' => $id]
        );
        if ($this->isCsrfTokenValid('delete' . $id, (string)$request->request->get('_token'))) {
            if ($request->request->get('_token') !== null) {
                if ($toolToDelete !== null) {
                    $toolToDelete->setDeleted(true);

                    $em->persist($toolToDelete);
                    $em->flush();

                    $cacheItemPool->deleteItem('api_projects');

                    $cache->delete('projects_list');
                    $cache->delete('tools_list');

                    $this->addFlash('success', 'L\'outil a bien été supprimé');
                    return $this->redirectToRoute('admin_tools_index');
                } else {
                    $this->addFlash('danger', 'Une erreur est survenue');
                    return $this->redirectToRoute('admin_tools_index');
                }
            } else {
                $this->addFlash('danger', 'Une erreur est survenue');
                return $this->redirectToRoute('admin_tools_index');
            }
        }
        return $this->redirectToRoute('admin_tools_index');
    }
}
