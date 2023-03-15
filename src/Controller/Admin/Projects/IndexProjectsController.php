<?php

namespace App\Controller\Admin\Projects;

use App\Repository\ProjectRepository;
use Symfony\Contracts\Cache\ItemInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/projects', name: 'admin_projects_')]
class IndexProjectsController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(
        ProjectRepository $projects,
        PaginatorInterface $paginator,
        Request $request,
        CacheInterface $cache
    ): Response {
        $cachedProjects = $cache->get('projects_list', function (ItemInterface $item) use ($projects) {
            $item->expiresAfter(3600);
            return $projects->findAllProjects();
        });

        $pagination = $paginator->paginate(
            $cachedProjects,
            $request->query->getInt('page', 1),
            12
        );

        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'Vous n\'avez pas les droits pour accéder à cette page');
            return $this->redirectToRoute('home');
        }

        return $this->render('admin/projects/index.html.twig', [
            'projects' => $pagination,
            'entity' => 'projects'
        ]);
    }
}
