<?php

namespace App\Controller\Admin\Backend;

use DateTimeImmutable;
use App\Entity\BackendLanguage;
use App\Form\BackendLanguageType;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/backendLanguages', name: 'admin_backendLanguages_')]
class AddBackendLanguagesController extends AbstractController
{
    #[Route('/add', name: 'create', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        CacheInterface $cache,
        CacheItemPoolInterface $cacheItemPool
    ): Response {
        $backendLanguage = new BackendLanguage();
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
            $backendLanguage->setSlug($slugger->slug($backendLanguage->getName() ? $backendLanguage->getName() : '')->lower());//phpcs:ignore

            $projects = [];
            foreach ($backendLanguage->getProjects() as $project) {
                $project->addBackendLanguage($backendLanguage);
                $projects[] = $project;
            }

            $backendLanguage->setCreatedAt(new DateTimeImmutable());
            $em->persist($backendLanguage);
            $em->flush();

            $cacheItemPool->deleteItem('api_projects');

            $cache->delete('projects_list');
            $cache->delete('backendLanguages_list');

            $this->addFlash('success', 'Le langage ' . $backendLanguage->getName() . ' a bien été ajouté !');
            return $this->redirectToRoute('admin_backendLanguages_index');
        }

        return $this->render('admin/backendLanguages/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
