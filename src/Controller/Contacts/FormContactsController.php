<?php

namespace App\Controller\Contacts;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Service\SendMailService;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/contacts', name: 'contacts_')]
class FormContactsController extends AbstractController
{
    #[Route('/form', name: 'form', methods: ['GET', 'POST'])]
    public function contact(
        Request $request,
        EntityManagerInterface $em,
        MailerInterface $mailer,
        SendMailService $mail
    ): Response {
        $contact = new Contact();

        $user = $this->getUser();

        if ($user !== null) {
            $contact->setEmail((string)$contact->getEmail());
            $contact->setFirstname((string)$contact->getFirstname());
            $contact->setLastname((string)$contact->getLastname());
        }

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contact = $form->getData();

            // Envoi de l'email
            $email = (new Email())
                ->from(
                    new Address(
                        $contact->getEmail(),
                        $contact->getFirstname() . ' ' . $contact->getLastname()
                    )
                )
                ->to('contact@edithbredon.fr')
                ->subject($contact->getSubject())
                ->text($contact->getMessage())
                ->html('Message de ' . $contact->getFirstname() . ' ' . $contact->getLastname() . ' : ' . '<p>' . $contact->getMessage() . '</p>');//phpcs:ignore

            // Si le captcha est coché, on envoie le mail
            $captcha_response = $request->request->get('g-recaptcha-response');
            if ($captcha_response) {
                $contact->setIsRead(false);
                $contact->setIsAnswered(false);
                $contact->setComment(null);
                $em->persist($contact);
                $em->flush();
                $mailer->send($email);
                $this->addFlash('success', 'Votre message a bien été envoyé');
                $mail->sendMail(
                    'no-reply@backoffice.edithbredon.fr',
                    $contact->getEmail(),
                    'Votre message a bien été envoyé',
                    'contact',
                    [
                        'contact' => $contact,
                    ]
                );
                return $this->redirectToRoute('app_login');
            } else {
                $this->addFlash('danger', 'Merci de cocher la case "Je ne suis pas un robot"');
                return $this->redirectToRoute('app_contact');
            }
        }

        return $this->render('contacts/form.html.twig', [
            'form' => $form->createView(),
            'contact' => $contact
        ]);
    }
}
