<?php

namespace App\Controller\Admin\Messages;

use App\Repository\ContactRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/messages', name: 'admin_messages_')]
class IndexMessagesController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(
        ContactRepository $contactRepository,
        PaginatorInterface $paginator,
        Request $request,
    ): Response {
        $cachedMessages = $contactRepository->findBy(
            ['deleted' => false, 'isAnswered' => false],
            ['createdAt' => 'DESC']
        );
        $pagination = $paginator->paginate(
            $cachedMessages,
            $request->query->getInt('page', 1),
            10
        );

        if (!$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('danger', 'Vous n\'avez pas les droits pour accéder à cette page');
            return $this->redirectToRoute('home');
        }

        return $this->render('admin/messages/index.html.twig', [
            'messages' => $pagination,
            'entity' => 'messages',
        ]);
    }
}
