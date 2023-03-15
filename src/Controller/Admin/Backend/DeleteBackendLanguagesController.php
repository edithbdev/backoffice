<?php

namespace App\Controller\Admin\Backend;

use Psr\Cache\CacheItemPoolInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use App\Repository\BackendLanguageRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/backendLanguages', name: 'admin_backendLanguages_')]
class DeleteBackendLanguagesController extends AbstractController
{
    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(
        EntityManagerInterface $em,
        Request $request,
        BackendLanguageRepository $backendLanguage,
        string $id,
        CacheInterface $cache,
        CacheItemPoolInterface $cacheItemPool
    ): Response {
        $backendLanguageToDelete = $backendLanguage->findOneBy(
            ['id' => $id]
        );

        if ($this->isCsrfTokenValid('delete' . $id, (string)$request->get('_token'))) {
            if ($request->request->get('_token') !== null) {
                if ($backendLanguageToDelete !== null) {
                    $backendLanguageToDelete->setDeleted(true);

                    $em->persist($backendLanguageToDelete);
                    $em->flush();

                    $cacheItemPool->deleteItem('api_projects');

                    $cache->delete('projects_list');
                    $cache->delete('backendLanguages_list');

                    $this->addFlash('success', 'Le langage backend a bien été supprimé');
                    return $this->redirectToRoute('admin_backendLanguages_index');
                } else {
                    $this->addFlash('danger', 'Une erreur est survenue');
                    return $this->redirectToRoute('admin_backendLanguages_index');
                }
            } else {
                $this->addFlash('danger', 'Une erreur est survenue');
                return $this->redirectToRoute('admin_backendLanguages_index');
            }
        }
        return $this->redirectToRoute('admin_backendLanguages_index');
    }
}
