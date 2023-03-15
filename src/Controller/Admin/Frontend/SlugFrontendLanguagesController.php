<?php

namespace App\Controller\Admin\Frontend;

use App\Repository\ProjectRepository;
use App\Repository\FrontendLanguageRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/frontendLanguages', name: 'admin_frontendLanguages_')]
class SlugFrontendLanguagesController extends AbstractController
{
    #[Route('/{slug}', name: 'slug', requirements: ['slug' => '[a-z0-9\-]+'], methods: ['GET'])]
    public function slug(
        FrontendLanguageRepository $frontendLanguages,
        ProjectRepository $projects,
        string $slug
    ): Response {

        $frontendLanguage = $frontendLanguages->findOneBy(['slug' => $slug, 'deleted' => false]);

        if (!$frontendLanguage) {
            throw $this->createNotFoundException('Aucun langage frontend trouvé');
        }

        $projects = [];
        foreach ($frontendLanguage->getProjects() as $project) {
            if (!$project->getDeleted()) {
                $projects[] = $project;
            }
        }

        return $this->render('admin/frontendLanguages/slug.html.twig', [
            'frontendLanguage' => $frontendLanguage,
            'projects' => $projects ? $projects : null,
            'entity' => 'frontendLanguages',
        ]);
    }
}
