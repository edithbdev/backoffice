<?php

namespace App\Controller\Admin\Frontend;

use DateTimeImmutable;
use App\Entity\FrontendLanguage;
use App\Form\FrontendLanguageType;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/frontendLanguages', name: 'admin_frontendLanguages_')]
class AddFrontendLanguagesController extends AbstractController
{
    #[Route('/add', name: 'create', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        CacheInterface $cache,
        CacheItemPoolInterface $cacheItemPool
    ): Response {
        $frontendLanguage = new FrontendLanguage();
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
            $frontendLanguage->setSlug($slugger->slug($frontendLanguage->getName() ? $frontendLanguage->getName() : '')->lower());//phpcs:ignore

            $projects = [];
            foreach ($frontendLanguage->getProjects() as $project) {
                $project->addFrontendLanguage($frontendLanguage);
                $projects[] = $project;
            }

            $frontendLanguage->setCreatedAt(new DateTimeImmutable());
            $em->persist($frontendLanguage);
            $em->flush();

            $cacheItemPool->deleteItem('api_projects');

            $cache->delete('projects_list');
            $cache->delete('frontendLanguages_list');

            $this->addFlash('success', 'Le langage frontend' . $frontendLanguage->getName() . ' a bien été ajouté !');
            return $this->redirectToRoute('admin_frontendLanguages_index');
        }

        return $this->render('admin/frontendLanguages/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
