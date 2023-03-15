<?php

namespace App\Controller\Admin\Tools;

use Symfony\Contracts\Cache\ItemInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use App\Repository\ToolRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/tools', name: 'admin_tools_')]
class IndexToolsController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(
        ToolRepository $tools,
        PaginatorInterface $paginator,
        Request $request,
        CacheInterface $cache
    ): Response {
        $cachedTools = $cache->get('tools_list', function (ItemInterface $item) use ($tools) {
            $item->expiresAfter(3600);
            return $tools->findAllTools();
        });

        $pagination = $paginator->paginate(
            $cachedTools,
            $request->query->getInt('page', 1),
            12
        );

        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'Vous n\'avez pas les droits pour accéder à cette page');
            return $this->redirectToRoute('home');
        }

        return $this->render('admin/tools/index.html.twig', [
            'tools' => $pagination,
            'entity' => 'tools',
        ]);
    }
}
