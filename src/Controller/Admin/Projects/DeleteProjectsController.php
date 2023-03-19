<?php

namespace App\Controller\Admin\Projects;

use App\Entity\Enum\Status;
use App\Repository\ProjectRepository;
use Psr\Cache\CacheItemPoolInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/projects', name: 'admin_projects_')]
class DeleteProjectsController extends AbstractController
{
    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        EntityManagerInterface $em,
        ProjectRepository $project,
        string $id,
        CacheInterface $cache,
        CacheItemPoolInterface $cacheItemPool
    ): Response {
        $projectToDelete = $project->findOneBy(['id' => $id]);
        if ($this->isCsrfTokenValid('delete' . $id, (string)$request->request->get('_token'))) {
            if ($request->request->get('_token') !== null) {
                if ($projectToDelete !== null) {
                    $projectToDelete->setDeleted(true);
                    $projectToDelete->setStatus(Status::Archived);
                    $em->persist($projectToDelete);
                    $em->flush();

                    $cache->delete('projects_list');
                    $cache->delete('projects_list_archived');
                    $cache->delete('backendLanguages_list');
                    $cache->delete('frontendLanguages_list');
                    $cache->delete('tools_list');

                    $cacheItemPool->deleteItem('api_projects');

                    $this->addFlash(
                        'success',
                        'Le projet a bien été supprimé'
                    );
                    $currentView = $request->cookies->get('currentView');
                    return $this->redirectToRoute('admin_projects_index', ['currentView' => $currentView]);
                }
            }
        }
        $this->addFlash('danger', 'Une erreur est survenue');
        $currentView = $request->cookies->get('currentView');
        return $this->redirectToRoute('admin_projects_index', ['currentView' => $currentView]);
    }
}
