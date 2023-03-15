<?php

namespace App\Controller\Admin\Projects;

use App\Entity\Enum\Status;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/projects', name: 'admin_projects_')]
class ArchivedProjectsController extends AbstractController
{
    #[Route('/archived', name: 'archived', methods: ['GET'])]
    public function archived(
        ProjectRepository $projects,
        PaginatorInterface $paginator,
        Request $request,
        CacheInterface $cache,
    ): Response {
        $projects = $cache->get('projects_list_archived', function (ItemInterface $item) use ($projects) {
            $item->expiresAfter(3600);
            return $projects->findAllProjectsArchived();
        });

        $pagination = $paginator->paginate(
            $projects,
            $request->query->getInt('page', 1),
            12,
        );

        return $this->render('admin/projects/archived.html.twig', [
            'projects' => $pagination,
        ]);
    }

    #[Route('/archived/{id}', name: 'reactivate', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function reactivate(
        ProjectRepository $project,
        EntityManagerInterface $em,
        string $id,
        CacheInterface $cache
    ): Response {
        $projectToArchived = $project->findOneBy(['id' => $id]);

        if ($projectToArchived !== null) {
            $projectToArchived->setStatus(Status::Draft);
            $projectToArchived->setDeleted(false);
            $project->add($projectToArchived);
            $em->persist($projectToArchived);
            $em->flush();


            $cache->delete('projects_list');
            $cache->delete('projects_list_archived');
            $cache->delete('backendLanguages_list');
            $cache->delete('frontendLanguages_list');
            $cache->delete('tools_list');

            $this->addFlash('success', 'Le projet a bien été réactivé');

            return $this->redirectToRoute('admin_projects_index');
        } else {
            $this->addFlash('danger', 'Le projet n\'existe pas');
            return $this->redirectToRoute('admin_projects_index');
        }
    }
}
