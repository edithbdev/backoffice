<?php

namespace App\Controller\Admin\Messages;

use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/messages', name: 'admin_messages_')]
class DeleteMessagesController extends AbstractController
{
    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function delete(
        Request $request,
        ContactRepository $contact,
        EntityManagerInterface $em,
        string $id
    ): Response {
        $messageToDelete = $contact->findOneBy(['id' => $id]);
        if ($this->isCsrfTokenValid('delete' . $id, (string)$request->request->get('_token'))) {
            if ($request->request->get('_token') !== null) {
                if ($messageToDelete !== null) {
                    $messageToDelete->setDeleted(true);
                    $em->persist($messageToDelete);
                    $em->flush();
                    $this->addFlash('success', 'Le message a bien été supprimé');
                    return $this->redirectToRoute('admin_messages_index');
                } else {
                    $this->addFlash('danger', 'Une erreur est survenue');
                    return $this->redirectToRoute('admin_messages_index');
                }
            } else {
                $this->addFlash('danger', 'Une erreur est survenue');
                return $this->redirectToRoute('admin_messages_index');
            }
        }

        return $this->redirectToRoute('admin_messages_index');
    }
}
