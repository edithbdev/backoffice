<?php

namespace App\Controller\Admin;

use App\Entity\Project;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/projects', name: 'admin_projects_')]
class ProjectAdminController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ProjectRepository $projects): Response
    {
        return $this->render('admin/project/index.html.twig', [
            'projects' => $projects->findAll(),
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $em, ProjectRepository $projects): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
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
            $em->persist($project);
            $em->flush();
            $this->addFlash('success', 'Project created successfully');
            return $this->redirectToRoute('admin_projects_index');
        }

        return $this->render('admin/project/create.html.twig', [
            'form' => $form->createView(),
            // 'projects' => count($projects->findAll()) > 0 ? $projects->findAll() : null,
            'projects' => $projects->findAll(),
        ]);
    }

    // {} indique que c'est variable dans l'url
    #[Route('/{slug}', name: 'slug')]
    public function slug(Project $project): Response
    {
        // return $this->render('admin/project/slug.html.twig', [
        //     'project' => $project,
        // ]);

        // compact permet de créer un tableau associatif
        return $this->render('admin/project/slug.html.twig', compact('project'));
    }

    // #[Route('/edit', name: 'edit')]
    // public function edit(Project $project): Response
    // {
    //     return $this->render('admin/project/edit.html.twig', compact('project'));
    // }
}
