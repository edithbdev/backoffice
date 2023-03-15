<?php

namespace App\Controller\Admin\Backend;

use App\Repository\ProjectRepository;
use App\Repository\BackendLanguageRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/backendLanguages', name: 'admin_backendLanguages_')]
class SlugBackendLanguagesController extends AbstractController
{
    #[Route('/{slug}', name: 'slug', requirements: ['slug' => '[a-z0-9\-]+'], methods: ['GET'])]
    public function slug(
        BackendLanguageRepository $backendLanguages,
        ProjectRepository $projects,
        string $slug
    ): Response {

        $backendLanguage = $backendLanguages->findOneBy(['slug' => $slug, 'deleted' => false]);

        if (!$backendLanguage) {
            throw $this->createNotFoundException('Aucun langage backend trouvé');
        }

        $projects = [];
        foreach ($backendLanguage->getProjects() as $project) {
            if (!$project->getDeleted()) {
                $projects[] = $project;
            }
        }

        return $this->render('admin/backendLanguages/slug.html.twig', [
            'backendLanguage' => $backendLanguage,
            'projects' => $projects ? $projects : null,
            'entity' => 'backendLanguages',
        ]);
    }
}
