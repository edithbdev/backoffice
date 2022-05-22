<?php
namespace App\Controller;
use App\Form\ContactType;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Crypto\DkimSigner;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Gregwar\CaptchaBundle\Type\CaptchaType;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     * @param Request $request
     * @param MailerInterface $mailer
     * @return Response
     */
    public function index(Request $request, MailerInterface $mailer):Response
    {
        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);
         if($form->isSubmitted() && $form->isValid()) {
            $contactFormData = $form->getData();
            $email = new Email();
            $email->from(new Address($contactFormData['email'], $contactFormData['nom']));
            $email->to('contact@edithbredon.fr');
            $email->subject('Vous avez reçu un message de votre site');
            $email->text($contactFormData['message']);
            // If the user has a valid captcha, send the email
            if($form->get('captcha')->isValid()) {
                $mailer->send($email);
                // Display a success message to the user
                $this->addFlash('success', 'Votre message a bien été envoyé');
                // Redirect to the homepage
                return $this->redirectToRoute('home');
            } else {
                $this->addFlash('error', 'Le captcha est invalide');
            }
        }

        return $this->render ('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}


