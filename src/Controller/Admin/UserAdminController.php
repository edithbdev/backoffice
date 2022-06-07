<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Form\ResetPassType;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

/**
 * @isGranted("ROLE_ADMIN")
 * @Route("/admin/user", name="admin_user_")
 */
class UserAdminController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(UserRepository $users): Response
    {
        // All users
        $this->denyAccessUnlessGranted('POST_READ, $users');
        return $this->render('admin/user/index.html.twig', [
            'users' => $users->findBy([], ['username' => 'ASC']),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     */
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder
    ): Response {
        $user = new User();
        $this->denyAccessUnlessGranted(
            'POST_NEW',
            $user,
            'Unable to access this page!'
        );
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('password')->getData();
            if ($newPassword != null) {
                $encodedPassword = $passwordEncoder->encodePassword(
                    $user,
                    $newPassword
                );
                $user->setPassword($encodedPassword);
            }
            $user->setApiToken(md5(uniqid()));
            $user->setRoles(['ROLE_USER']);
            $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Le nouvel utilisateur a bien été créé');
            // $session->set("message", "Votre compte a bien été créé");

            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function show(User $user): Response
    {
        //accès géré dans le security.yaml
         $this->denyAccessUnlessGranted('POST_READ, $user');
        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET", "POST"}, requirements={"id"="\d+"})
     */
    public function edit(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager,
        UserPasswordEncoderInterface $passwordEncoder,
        Session $session,
        $id
    ): Response {
        $this->denyAccessUnlessGranted(
            'POST_EDIT',
            $user,
            'Unable to access this page!'
        );
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPassword = $form->get('password')->getData();

            if ($newPassword != null) {
                $encodedPassword = $passwordEncoder->encodePassword(
                    $user,
                    $newPassword
                );
                $user->setPassword($encodedPassword);
            }
            if ($user->getRoles() == null) {
                $user->setRoles(['ROLE_USER']);
            }
            $user->setUpdatedAt(new \DateTimeImmutable());

           $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'success',
                'L\'utilisateur ' .
                    $user->getUsername() .
                    ' à bien été modifié.'
            );

            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"DELETE", "POST"}, requirements={"id"="\d+"})
     * @ParamConverter("id", class="App\Entity\User", options={"id"="id"})
     */
    public function delete(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager,
        Session $session,
        $id
    ): Response {
         $this->denyAccessUnlessGranted(
            'POST_DELETE',
            $user,
            'Unable to access this page!'
        );
        // si l'utilisateur connecté est le même que l'utilisateur à supprimer
        if ($this->getUser() !== $user->getId()) {
            $entityManager->remove($user);
            $entityManager->flush();
        } else {
            $this->addFlash(
                'danger',
                'Vous ne pouvez pas vous supprimer vous-même.'
            );
        }

         // Before deleting $user, we check the token
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }
        $this->addFlash(
            'success',
            'L\'utilisateur ' .
                $user->getUsername() .
                ' à bien été supprimé.'
        );
            // permet de fermer la session utilisateur et d'éviter que l'EntityProvider ne trouve pas la session
            // On supprime la session de l'utilisateur
            $session->invalidate();


       return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
    }
}

