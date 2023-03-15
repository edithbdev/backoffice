<?php

namespace App\Controller\Admin\Backend;

use Symfony\Contracts\Cache\ItemInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\BackendLanguageRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/backendLanguages', name: 'admin_backendLanguages_')]
class IndexBackendLanguagesController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(
        BackendLanguageRepository $backendLanguages,
        PaginatorInterface $paginator,
        Request $request,
        CacheInterface $cache
    ): Response {
        $cachedBackendLanguages = $cache->get('backendLanguages_list', function (ItemInterface $item) use ($backendLanguages) {//phpcs:ignore
            $item->expiresAfter(3600);
            return $backendLanguages->findAllBackendLanguages();
        });

        $pagination = $paginator->paginate(
            $cachedBackendLanguages,
            $request->query->getInt('page', 1),
            12
        );

        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'Vous n\'avez pas les droits pour accéder à cette page');
            return $this->redirectToRoute('home');
        }

        return $this->render('admin/backendLanguages/index.html.twig', [
            'backendLanguages' => $pagination,
            'entity' => 'backendLanguages',
        ]);
    }
}
