<?php

namespace App\Controller\Admin\Projects;

use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/projects', name: 'admin_projects_')]
class SlugProjectsController extends AbstractController
{
    // {} indicates that it is variable in the url
    #[Route('/{slug}', name: 'slug', requirements: ['slug' => '[a-z0-9\-]+'], methods: ['GET'])]
    public function slug(
        ProjectRepository $projects,
        string $slug,
    ): Response {

        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'Vous n\'avez pas les droits pour accéder à cette page');
            return $this->redirectToRoute('home');
        }

        $project = $projects->findOneBy(['slug' => $slug, 'deleted' => false]);

        if (!$project) {
            throw $this->createNotFoundException('Aucun projet trouvé');
        }

        return $this->render('admin/projects/slug.html.twig', [
            'project' => $project,
            'entity' => 'projects',
        ]);
    }
}
