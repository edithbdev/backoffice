<?php

namespace App\Controller\Admin\Messages;

use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/messages', name: 'admin_messages_')]
class ShowMessagesController extends AbstractController
{
    #[Route('/show/{id}', name: 'show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(
        ContactRepository $contacts,
        EntityManagerInterface $em,
        string $id,
    ): Response {

        $message = $contacts->findOneBy(['id' => $id]);

        if (!$message) {
            throw $this->createNotFoundException('Le message n\'existe pas');
        }

        if ($message->getIsRead() === false) {
            $message->setIsRead(true);
            $em->persist($message);
            $em->flush();
        }

        return $this->render('admin/messages/show.html.twig', [
            'message' => $message,
            'entity' => 'messages',
        ]);
    }
}
