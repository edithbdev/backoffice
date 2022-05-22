<?php

namespace App\Controller\Admin;

use DateTimeImmutable;
use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin/project", name="admin_project_")
 */
class ProjectAdminController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @return Response
     */
    public function index(ProjectRepository $projects): Response
    {
        // All projects sort by date desc
        return $this->render('admin/project/index.html.twig', [
            'projects' => $projects->findBy([], ['created_At' => 'DESC']),
        ]);
    }

    /**
     * @Route("/new", name="new", methods={"GET", "POST"})
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $project = new Project();
        // Access rights to the project
        $this->denyAccessUnlessGranted(
            // The role to check
            'POST_NEW',
            // The project to check
            $project,
            // The message to display if the user doesn't have the role
            'Unable to access this page!'
        );
        // Form to create a new project
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadFile = $form->get('image')->getData();
            if ($uploadFile != null) {
                $destination =
                    $this->getParameter('kernel.project_dir') .
                    '/public/uploads/';
                $originalFilename = pathinfo(
                    $uploadFile->getClientOriginalName(),
                    PATHINFO_FILENAME
                );
                $newFilename =
                    $originalFilename .
                    '-' .
                    uniqid() .
                    '.' .
                    $uploadFile->guessExtension();
                $uploadFile->move($destination, $newFilename);
                $project->setImage($newFilename);
            }

            $techno = $form->get('techno')->getData();
            foreach ($project as $techno => $value) {
                $project->addTechno($techno);
            }

            $project->setCreatedAt(new DateTimeImmutable());
            $this->getDoctrine()->getManager();
            $entityManager->persist($project);
            $entityManager->flush();

            $this->addFlash('success', 'Projet créé avec succès');
            return $this->redirectToRoute(
                'admin_project_index',
                [],
                Response::HTTP_SEE_OTHER // code 303 https://developer.mozilla.org/fr/docs/Web/HTTP/Status/303
            );
        }

        return $this->render('admin/project/new.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"}, requirements={"id"="\d+"}) // id must be an integer
     * @param Project $project
     * @return Response
     */
    public function show(Project $project): Response
    {
        return $this->render('admin/project/show.html.twig', [
            'project' => $project,
        ]);
    }

    /**
     * @Route("/edit/{id}", name="edit", methods={"GET", "POST"}, requirements={"id"="\d+"})
     * @param Project $project
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function edit(
        Request $request,
        Project $project,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted(
            'POST_EDIT',
            $project,
            'Unable to access this page!'
        );
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $uploadFile = $form->get('image')->getData();
            if ($uploadFile != null) {
                $destination =
                    $this->getParameter('kernel.project_dir') .
                    '/public/uploads/';
                $originalFilename = pathinfo(
                    $uploadFile->getClientOriginalName(),
                    PATHINFO_FILENAME
                );
                $newFilename =
                    $originalFilename .
                    '-' .
                    uniqid() .
                    '.' .
                    $uploadFile->guessExtension();
                $uploadFile->move($destination, $newFilename);
                $project->setImage($newFilename);
            }
            $technos = $form->get('techno')->getData();
            foreach ($technos as $techno) {
                $project->addTechno($techno);
            }

            $this->getDoctrine()->getManager();
            $entityManager->persist($project);
            $entityManager->flush();

            $this->addFlash('success', 'Projet modifié avec succès');
            return $this->redirectToRoute('admin_project_index');
        }
        return $this->render('admin/project/edit.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="delete", methods={"POST", "DELETE"}, requirements={"id"="\d+"})
     * @param Project $project
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function delete(
        Project $project,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {
        $this->denyAccessUnlessGranted(
            'POST_DELETE',
            $project,
            'Unable to access this page!'
        );
        // Before deleting the project, we check the token
       if ($this->isCsrfTokenValid('delete' . $project->getId(), $request->request->get('_token'))) {
            $entityManager->remove($project);
            $entityManager->flush();
        }
        $this->addFlash('success', 'Projet supprimé avec succès');

        return $this->redirectToRoute('admin_project_index', [], Response::HTTP_SEE_OTHER);

            // If the token is not valid, we throw an Access Denied exception
        throw $this->createAccessDeniedException('Le token n\'est pas valide.');
    }

}
