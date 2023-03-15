<?php

namespace App\Controller\Admin\Messages;

use DateTimeImmutable;
use App\Form\MessageType;
use App\Repository\ContactRepository;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/messages', name: 'admin_messages_')]
class EditMessagesController extends AbstractController
{
    #[Route('/edit/{id}', name: 'update', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(
        ContactRepository $contacts,
        Request $request,
        EntityManagerInterface $em,
        string $id,
    ): Response {

        $message = $contacts->findOneBy(['id' => $id]);

        if (!$message) {
            throw $this->createNotFoundException('Aucun message trouvé');
        }

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        $message = $form->getData();

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
            if ($message->getEmail() === null) {
                $message->setEmail(null);
            } else {
                $message->setEmail($message->getEmail());
            }

            if ($message->getIsRead() === true) {
                $message->setIsRead(true);
            } else {
                $message->setIsRead(false);
            }

            if ($message->getIsAnswered() === true) {
                $message->setIsAnswered(true);
            } else {
                $message->setIsAnswered(false);
            }

            if ($message->getComment() === null) {
                $message->setComment(null);
            } else {
                $message->setComment($message->getComment());
            }

            $message->setUpdatedAt(new DateTimeImmutable());

            $em->persist($message);
            $em->flush();
            $this->addFlash('success', 'Le message a bien été modifié');
            return $this->redirectToRoute('admin_messages_index');
        }

        return $this->render('admin/messages/edit.html.twig', [
            'form' => $form->createView(),
            'message' => $message ?? null,
        ]);
    }
}
