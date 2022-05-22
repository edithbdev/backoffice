<?php

namespace App\Controller\Api;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api/project", name="api_project_")
 */
class ProjectApiController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     * @return Response
     */
    public function index(ProjectRepository $projectRepository): Response
    {
        // Retrieve all projects
        $projects = $projectRepository->findBy(['isPublished' => true]);
        // Projects are serialized in JSON
        // and sent to the client
        // in the body of the response
        // with the HTTP status code 200 (OK)
        // Serialization
        // Serialization de $projects en JSON qui permet de l'afficher dans le navigateur
        return $this->json($projects,
        Response::HTTP_OK,
        [], ['groups' => 'project_read']);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function show(Project $project): Response
    {
        // Récupération du projet
        $project = $this->getDoctrine()
            ->getRepository(Project::class)
            ->find($project->getId());

        return $this->json($project, Response::HTTP_OK, [], ['groups' => 'project_read']);
    }
}

