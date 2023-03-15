<?php

namespace App\Controller\Admin\Messages;

use App\Repository\ContactRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/messages', name: 'admin_messages_')]
class ArchivedMessagesController extends AbstractController
{
    #[Route('/archived', name: 'archived', methods: ['GET'])]
    public function archived(
        ContactRepository $contactRepository,
        PaginatorInterface $paginator,
        Request $request,
    ): Response {
        $messages = $contactRepository->findMessagesToArchive();
        $pagination = $paginator->paginate(
            $messages,
            $request->query->getInt('page', 1),
            10
        );
        return $this->render('admin/messages/archived.html.twig', [
            'messages' => $pagination,
        ]);
    }
}
