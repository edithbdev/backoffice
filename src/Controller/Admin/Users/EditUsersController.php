<?php

namespace App\Controller\Admin\Users;

use App\Form\UserType;
use DateTimeImmutable;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin/users', name: 'admin_users_')]
class EditUsersController extends AbstractController
{
    #[Route('/edit/{id}', name: 'update', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        EntityManagerInterface $em,
        UserRepository $users,
        UserPasswordHasherInterface $passwordEncoder,
        SluggerInterface $slugger,
        string $id,
        CacheInterface $cache
    ): Response {
        $user = $users->findOneBy(
            ['id' => $id, 'deleted' => false]
        );

        if (!$user) {
            throw $this->createNotFoundException(
                'No user found for id ' . $id
            );
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        $user = $form->getData();

        $data = [];

        if ($form->isSubmitted() && !$form->isValid()) {
            $errors = $form->getErrors(true, true);
            foreach ($errors as $error) {
                if ($error instanceof FormError) {
                    $data[] = $error->getMessage();
                }
            }
            // return $this->json($data, 400);
        }

        // https://symfony.com/doc/current/controller/upload_file.html
        if ($form->isSubmitted() && $form->isValid()) {
            if (
                $user->getSlug() != $slugger->slug($user->getFirstname()) .
                '-' .
                $slugger->slug(
                    $user->getLastname()
                )
            ) {
                $user
                    ->setSlug(
                        $slugger->slug($user->getFirstname())->lower() .
                            '-' .
                            $slugger->slug(
                                $user->getLastname()
                            )
                            ->lower()
                    );
            }

            $user->setPassword(
                $passwordEncoder->hashPassword($user, $user->getPassword())
            );

            if ($user->getRoles() == null) {
                $user->setRoles(['ROLE_USER']);
            }

            $user->setUpdatedAt(new DateTimeImmutable());
            $em->persist($user);
            $em->flush();

            $cache->delete('users_list');

            $this->addFlash(
                'success',
                'User ' .
                    $user->getFirstname() .
                    ' a été modifié avec succès'
            );
            return $this->redirectToRoute('admin_users_index');
        }

        return $this->render('admin/users/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
}
