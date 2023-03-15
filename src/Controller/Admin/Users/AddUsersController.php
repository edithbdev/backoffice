<?php

namespace App\Controller\Admin\Users;

use App\Entity\User;
use App\Form\UserType;
use DateTimeImmutable;
use App\Service\SendMailService;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

#[Route('/admin/users', name: 'admin_users_')]
class AddUsersController extends AbstractController
{
    #[Route('/add', name: 'create', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordEncoder,
        SluggerInterface $slugger,
        CacheInterface $cache,
        SendMailService $mail,
        TokenGeneratorInterface $tokenGenerator
    ): Response {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

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
            if ($passwordEncoder != null) {
                $user
                    ->setSlug(
                        $slugger->slug((string)$user->getFirstname())->lower() .
                            '-' .
                            $slugger->slug(
                                (string)$user->getLastname()
                            )
                            ->lower()
                    );
                $user->setPassword(
                    $passwordEncoder->hashPassword($user, (string)$user->getPassword())
                );

                $user->setCreatedAt(new DateTimeImmutable());
                $user->setRoles(['ROLE_USER']);

                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $em->persist($user);
                $em->flush();

                $url = $this->generateUrl(
                    'verify_email',
                    ['token' => $token],
                    UrlGeneratorInterface::ABSOLUTE_URL
                );

                $mail->sendMail(
                    'no-reply@backoffice.edithbredon.fr',
                    (string)$user->getEmail(),
                    'Vérification de votre adresse email',
                    'email_verification',
                    ['user' => $user, 'url' => $url]
                );

                $cache->delete('users_list');

                $this->addFlash(
                    'success',
                    'User ' . $user->getFirstname() . ' a été créé et un email de vérification a été envoyé'
                );

                return $this->redirectToRoute('admin_users_index');
            } else {
                $this->addFlash('danger', 'une erreur est survenue');
                return $this->redirectToRoute('admin_users_create');
            }
        }
        return $this->render('admin/users/new.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
