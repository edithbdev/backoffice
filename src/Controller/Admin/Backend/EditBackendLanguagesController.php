<?php

namespace App\Controller\Admin\Backend;

use App\Form\BackendLanguageType;
use App\Repository\ProjectRepository;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use App\Repository\BackendLanguageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/backendLanguages', name: 'admin_backendLanguages_')]
class EditBackendLanguagesController extends AbstractController
{
    #[Route('/edit/{id}', name: 'update', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(
        BackendLanguageRepository $backendLanguages,
        Request $request,
        ProjectRepository $projectsRepository,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        string $id,
        CacheInterface $cache,
        CacheItemPoolInterface $cacheItemPool
    ): Response {
        $backendLanguage = $backendLanguages->findOneBy(
            ['id' => $id, 'deleted' => false]
        );

        if (!$backendLanguage) {
            throw $this->createNotFoundException('Aucun langage backend trouvé');
        }

        $form = $this->createForm(BackendLanguageType::class, $backendLanguage);
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
            $backendLanguage = $form->getData();
            if ($form->get('name')->getData() !== $backendLanguage->getName()) {
                $backendLanguage->setSlug($slugger->slug($backendLanguage->getName())->lower());
            }

            $projects = $projectsRepository->findBy(['deleted' => false]);
            foreach ($projects as $project) {
                if ($form->get('projects')->getData()->contains($project)) {
                    $project->addBackendLanguage($backendLanguage);
                } else {
                    $project->removeBackendLanguage($backendLanguage);
                }
            }

            $backendLanguage->setUpdatedAt(new \DateTimeImmutable());
            $em->persist($backendLanguage);
            $em->flush();

            $cacheItemPool->deleteItem('api_projects');

            $cache->delete('projects_list');
            $cache->delete('backendLanguages_list');

            $this->addFlash('success', 'Le langage backend a bien été modifié');
            return $this->redirectToRoute('admin_backendLanguages_index');
        }
        return $this->render('admin/backendLanguages/edit.html.twig', [
            'form' => $form->createView(),
            'backendLanguage' => $backendLanguage,
        ]);
    }
}
