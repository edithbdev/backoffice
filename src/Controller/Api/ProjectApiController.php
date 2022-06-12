<?php

namespace App\Controller\Api;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/api", name="api_")
 */
class ProjectApiController extends AbstractController
{
    /**
     * @Route("/projects", name="project_index", methods={"GET"})
     * @return Response
     */
    public function index(ProjectRepository $projectRepository): Response
    {
        // // page accessible pour tous le monde
        // $projects = $projectRepository->findAll();

        // return $this->json($projects, 200, [], ['groups' => 'project:read']);

        // Retrieve all projects
        $projects = $projectRepository->findBy(['isPublished' => true]);

         if (!$projects) {
            return $this->json(['message' => 'No projects found'], Response::HTTP_NOT_FOUND);
        }
        $data = [];

        // Loop through projects and add to array
        foreach ($projects as $project) {
            $data[] = [
                'id' => $project->getId(),
                'name' => $project->getName(),
                'description' => $project->getDescription(),
                'image' => $project->getImage(),
                'link' => $project->getLink(),
                'techno' => $project->getTechno(),
                'created_at' => $project->getCreatedAt(),
            ];
        }
        // Projects are serialized in JSON
        // and sent to the client
        // in the body of the response
        // with the HTTP status code 200 (OK)
        // Serialization
        // Serialization de $projects en JSON qui permet de l'afficher dans le navigateur

        return $this->json($data,
        Response::HTTP_OK,
        [], ['groups' => 'project_read']);

        // return $this->json($data, Response::HTTP_OK);
    }

    /**
     * @Route("/projects/{id}", name="project_show", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function show(int $id): Response
    {
          // Récupération du projet
        $project = $this->getDoctrine()
            ->getRepository(Project::class)
            ->find($id);

        if (!$project) {
            return $this->json('No project found for id' . $id, 404);
        }

        $data = [
            'id' => $project->getId(),
            'name' => $project->getName(),
            'description' => $project->getDescription(),
            'image' => $project->getImage(),
            'link' => $project->getLink(),
            'techno' => $project->getTechno(),
            'created_at' => $project->getCreatedAt(),
        ];

        return $this->json($data, Response::HTTP_OK, [], ['groups' => 'project_read']);
    }
}

