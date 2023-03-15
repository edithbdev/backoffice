<?php

namespace App\Controller\Api\Projects;

use App\Entity\Enum\Status;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;

class SimilarApiProjectsController extends AbstractController
{
    private ProjectRepository $projectRepository;
    private SerializerInterface $serializer;

    public function __construct(ProjectRepository $projectRepository, SerializerInterface $serializer)
    {
        $this->projectRepository = $projectRepository;
        $this->serializer = $serializer;
    }

    #[Route('/api/projects/{id}/similar', name: 'api_projects_similar', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])] //phpcs:ignore
    public function similar(string $id): Response
    {
        $project = $this->projectRepository->findOneBy(['id' => $id]);

        if (!$project) {
            return $this->json([
                'message' => 'Au projet trouvé',
            ], 404);
        }

        $frontendLanguagesData = $project->getFrontendLanguages();
        $frontendLanguages = $frontendLanguagesData->toArray();

        $backendLanguagesData = $project->getBackendLanguages();
        $backendLanguages = $backendLanguagesData->toArray();

        $toolsData = $project->getTools();
        $tools = $toolsData->toArray();

        $projects = $this->projectRepository->findSimilarProjects($frontendLanguages, $backendLanguages, $tools);

        $projectsData = [];

        foreach ($projects as $project) {
            if ($project->getId() != $id && $project->getStatus() == Status::Published) {
                $projectsData[] = [
                    'id' => $project->getId(),
                    'name' => $project->getName(),
                    'year' => $project->getYear(),
                    'description' => strip_tags((string)$project->getDescription()),
                    'imageName' => $project->getImageName(),
                    'imageFile' => $project->getImageFile(),
                    'projectLink' => $project->getProjectLink(),
                    'githubLink' => $project->getGithubLink(),
                    'slug' => $project->getSlug(),
                    'createdAt' => $project->getCreatedAt(),
                    'updatedAt' => $project->getUpdatedAt(),
                ];
            }
        }

        $context = (new ObjectNormalizerContextBuilder())
        ->withContext([
            'groups' => ['project_list'],
            DateTimeNormalizer::FORMAT_KEY => 'd/m/Y',
        ])
        ->toArray();

        $serializedSimilarProjects = $this->serializer->serialize($projectsData, 'json', $context);

        return new JsonResponse($serializedSimilarProjects, Response::HTTP_OK, [
            'Content-Type' => 'application/json'
        ], true);
    }

    #[Route('/api/projects/{slug}/similar', name: 'api_projects_similar_slug', methods: ['GET', 'POST'], requirements: ['slug' => '[a-z0-9\-]+'])] //phpcs:ignore
    public function similarSlug(string $slug): Response
    {
        $project = $this->projectRepository->findOneBy(['slug' => $slug]);

        if (!$project) {
            return $this->json([
                'message' => 'Au projet trouvé',
            ], 404);
        }

        $frontendLanguagesData = $project->getFrontendLanguages();
        $frontendLanguages = $frontendLanguagesData->toArray();

        $backendLanguagesData = $project->getBackendLanguages();
        $backendLanguages = $backendLanguagesData->toArray();

        $toolsData = $project->getTools();
        $tools = $toolsData->toArray();

        $projects = $this->projectRepository->findSimilarProjects($frontendLanguages, $backendLanguages, $tools);

        $projectsData = [];

        foreach ($projects as $project) {
            if ($project->getSlug() != $slug && $project->getStatus() == Status::Published) {
                $projectsData[] = [
                    'id' => $project->getId(),
                    'name' => $project->getName(),
                    'year' => $project->getYear(),
                    'description' => strip_tags((string)$project->getDescription()),
                    'imageName' => $project->getImageName(),
                    'imageFile' => $project->getImageFile(),
                    'projectLink' => $project->getProjectLink(),
                    'githubLink' => $project->getGithubLink(),
                    'slug' => $project->getSlug(),
                    'createdAt' => $project->getCreatedAt(),
                    'updatedAt' => $project->getUpdatedAt(),
                ];
            }
        }

        $context = (new ObjectNormalizerContextBuilder())
        ->withContext([
            'groups' => ['project_list'],
            DateTimeNormalizer::FORMAT_KEY => 'd/m/Y',
        ])
        ->toArray();

        $serializedSimilarProjects = $this->serializer->serialize($projectsData, 'json', $context);

        return new JsonResponse($serializedSimilarProjects, Response::HTTP_OK, [
            'Content-Type' => 'application/json'
        ], true);
    }
}
