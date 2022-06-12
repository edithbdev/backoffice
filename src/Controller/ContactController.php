<?php
namespace App\Controller;
use App\Form\ContactType;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     * @param Request $request
     * @param MailerInterface $mailer
     * @return Response
     */
    public function index(Request $request, MailerInterface $mailer ):Response
    {
        $form = $this->createForm(ContactType::class);

        $form->handleRequest($request);

         if($form->isSubmitted() && $form->isValid()) {
            $contactFormData = $form->getData();
            $email = new Email();
            $email->from(new Address($contactFormData['email'], $contactFormData['name']));
            $email->to('contact@edithbredon.fr');
            $email->subject('You have a new message from your website');
            $email->text($contactFormData['message']);

            if ($form->getData()['g-recaptcha-response'] === null) {
                $this->addFlash('error', 'Please check the reCAPTCHA');
                return $this->redirectToRoute('contact');
                } else {
                      $mailer->send($email);
                $this->addFlash('success', 'Your message has been sent. Thank you!');
                return $this->redirectToRoute('app_login');
                }
            }
            // else {
            //     $this->addFlash('error', 'Please check the reCAPTCHA');
            //     return $this->redirectToRoute('contact');
            // }

        return $this->render ('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}


