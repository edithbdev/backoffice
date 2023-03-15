<?php

namespace App\Controller\Admin\Frontend;

use Symfony\Contracts\Cache\ItemInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\FrontendLanguageRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/frontendLanguages', name: 'admin_frontendLanguages_')]
class IndexFrontendLanguagesController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(
        FrontendLanguageRepository $frontendLanguages,
        PaginatorInterface $paginator,
        Request $request,
        CacheInterface $cache
    ): Response {
        $cachedFrontendLanguages = $cache->get('frontendLanguages_list', function (ItemInterface $item) use ($frontendLanguages) {//phpcs:ignore
            $item->expiresAfter(3600);
            return $frontendLanguages->findAllFrontendLanguages();
        });

        $pagination = $paginator->paginate(
            $cachedFrontendLanguages,
            $request->query->getInt('page', 1),
            12
        );

        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'Vous n\'avez pas les droits pour accéder à cette page');
            return $this->redirectToRoute('home');
        }

        return $this->render('admin/frontendLanguages/index.html.twig', [
            'frontendLanguages' => $pagination,
            'entity' => 'frontendLanguages',
        ]);
    }
}
