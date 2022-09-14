<?php

namespace App\Controller\Admin;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/projects', name: 'admin_projects_')]
class ProjectController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProjectRepository $projects): Response
    {
        return $this->render('admin/project/index.html.twig', [
            'projects' => $projects->findAll(),
        ]);
    }

    // {} indique que c'est variable dans l'url
    #[Route('/{slug}', name: 'slug')]
    public function slug(Project $project): Response
    {
        // return $this->render('admin/project/slug.html.twig', [
        //     'project' => $project,
        // ]);

        // compact permet de créer un tableau associatif
        return $this->render('admin/project/slug.html.twig', compact('project'));
    }
}
