<?php

namespace App\Controller\Admin\Frontend;

use Psr\Cache\CacheItemPoolInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\FrontendLanguageRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/frontendLanguages', name: 'admin_frontendLanguages_')]
class DeleteFrontendLanguagesController extends AbstractController
{
    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(
        EntityManagerInterface $em,
        Request $request,
        FrontendLanguageRepository $frontendLanguage,
        string $id,
        CacheInterface $cache,
        CacheItemPoolInterface $cacheItemPool
    ): Response {
        $frontendLanguageToDelete = $frontendLanguage->findOneBy(
            ['id' => $id]
        );

        if ($this->isCsrfTokenValid('delete' . $id, (string)$request->get('_token'))) {
            if ($request->request->get('_token') !== null) {
                if ($frontendLanguageToDelete !== null) {
                    $frontendLanguageToDelete->setDeleted(true);

                    $em->persist($frontendLanguageToDelete);
                    $em->flush();

                    $cacheItemPool->deleteItem('api_projects');

                    $cache->delete('projects_list');
                    $cache->delete('frontendLanguages_list');

                    $this->addFlash('success', 'Le langage frontend a bien été supprimé');
                    return $this->redirectToRoute('admin_frontendLanguages_index');
                } else {
                    $this->addFlash('danger', 'Une erreur est survenue');
                    return $this->redirectToRoute('admin_frontendLanguages_index');
                }
            } else {
                $this->addFlash('danger', 'Une erreur est survenue');
                return $this->redirectToRoute('admin_frontendLanguages_index');
            }
        }
        return $this->redirectToRoute('admin_frontendLanguages_index');
    }
}
