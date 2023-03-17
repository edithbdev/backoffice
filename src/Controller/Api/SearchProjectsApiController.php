<?php

namespace App\Controller\Api;

use App\Entity\Enum\Status;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;

class SearchProjectsApiController extends AbstractController
{
    private ProjectRepository $projectRepository;
    private SerializerInterface $serializer;

    public function __construct(ProjectRepository $projectRepository, SerializerInterface $serializer)
    {
        $this->projectRepository = $projectRepository;
        $this->serializer = $serializer;
    }

    #[Route('/api/search', name: 'api_search', methods: ['POST', 'GET'])]
    public function index(): Response
    {
        // on récupère la recherche
        $search = $_GET['search'];
        // navigateur : http://localhost:8000/api/search?search=php
        // On récupère les projets correspondant à la recherche
        $projects = $this->projectRepository->findBySearch($search);
        $projectsData = [];

        foreach ($projects as $project) {
            //on ne récupère que les projets publiés
            if ($project->getStatus() !== Status::Published) {
                continue;
            }
            $projectsData[] = [
                'id' => $project->getId(),
                'name' => $project->getName(),
                'year' => $project->getYear(),
                'status' => $project->getStatus(),
                'slug' => $project->getSlug(),
                'description' => strip_tags((string)$project->getDescription()),
                'imageName' => $project->getImageName() ?? '',
                'imageFile' => $project->getImageFile() ?? '',
                'projectLink' => $project->getProjectlink() ?? '',
                'githubLink' => $project->getGithubLink() ?? '',
                'backendLanguages' => $project->getBackendLanguages(),
                'frontendLanguages' => $project->getFrontendLanguages(),
                'tools' => $project->getTools(),
                'createdAt' => $project->getCreatedAt(),
                'updatedAt' => $project->getUpdatedAt(),
            ];
        }

        $context = (new ObjectNormalizerContextBuilder())
        ->withContext([
            'groups' => ['project_list'],
            DateTimeNormalizer::FORMAT_KEY => 'd/m/Y',
        ])
        ->toArray();

        $serializedProjects = $this->serializer->serialize(["results" => $projectsData], 'json', $context);

        return new JsonResponse($serializedProjects, Response::HTTP_OK, [
            'Content-Type' => 'application/json'
        ], true);
    }
}
