<?php

namespace App\Controller\Admin\Frontend;

use App\Form\FrontendLanguageType;
use App\Repository\ProjectRepository;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\FrontendLanguageRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/frontendLanguages', name: 'admin_frontendLanguages_')]
class EditFrontendLanguagesController extends AbstractController
{
    #[Route('/edit/{id}', name: 'update', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(
        FrontendLanguageRepository $frontendLanguages,
        Request $request,
        ProjectRepository $projectsRepository,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        string $id,
        CacheInterface $cache,
        CacheItemPoolInterface $cacheItemPool
    ): Response {
        $frontendLanguage = $frontendLanguages->findOneBy(
            ['id' => $id, 'deleted' => false]
        );

        if (!$frontendLanguage) {
            throw $this->createNotFoundException('Aucun langage frontend trouvé');
        }

        $form = $this->createForm(FrontendLanguageType::class, $frontendLanguage);
        $form->handleRequest($request);

        $data = [];

        if ($form->isSubmitted() && !$form->isValid()) {
            $errors = $form->getErrors(true, true);
            foreach ($errors as $error) {
                if ($error instanceof FormError) {
                    $data[] = $error->getMessage();
                }
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $frontendLanguage = $form->getData();
            if ($form->get('name')->getData() !== $frontendLanguage->getName()) {
                $frontendLanguage->setSlug($slugger->slug($frontendLanguage->getName())->lower());
            }

            $projects = $projectsRepository->findBy(['deleted' => false]);
            foreach ($projects as $project) {
                if ($form->get('projects')->getData()->contains($project)) {
                    $project->addFrontendLanguage($frontendLanguage);
                } else {
                    $project->removeFrontendLanguage($frontendLanguage);
                }
            }

            $frontendLanguage->setUpdatedAt(new \DateTimeImmutable());
            $em->persist($frontendLanguage);
            $em->flush();

            $cacheItemPool->deleteItem('api_projects');

            $cache->delete('projects_list');
            $cache->delete('frontendLanguages_list');

            $this->addFlash('success', 'Le langage frontend a bien été modifié');
            return $this->redirectToRoute('admin_frontendLanguages_index');
        }
        return $this->render('admin/frontendLanguages/edit.html.twig', [
            'form' => $form->createView(),
            'frontendLanguage' => $frontendLanguage,
        ]);
    }
}
