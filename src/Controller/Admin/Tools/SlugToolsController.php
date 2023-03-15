<?php

namespace App\Controller\Admin\Tools;

use App\Repository\ProjectRepository;
use App\Repository\ToolRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/tools', name: 'admin_tools_')]
class SlugToolsController extends AbstractController
{
    #[Route('/{slug}', name: 'slug', requirements: ['slug' => '[a-z0-9\-]+'], methods: ['GET'])]
    public function slug(
        ToolRepository $tool,
        ProjectRepository $projects,
        string $slug
    ): Response {
        $tools = $tool->findOneBy(['slug' => $slug, 'deleted' => false]);

        if (!$tools) {
            throw $this->createNotFoundException('Programming Language not found');
        }

        $projects = [];
        foreach ($tools->getProjects() as $project) {
            if (!$project->getDeleted()) {
                $projects[] = $project;
            }
        }

        return $this->render('admin/tools/slug.html.twig', [
            'tool' => $tools,
            'projects' => $projects ? $projects : null,
            'entity' => 'tools',
        ]);
    }
}
