<?php

namespace App\Controller\Api\Projects;

use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;

class ShowApiProjectsController extends AbstractController
{
    private ProjectRepository $projectRepository;
    private SerializerInterface $serializer;

    public function __construct(
        ProjectRepository $projectRepository,
        SerializerInterface $serializer
    ) {
        $this->projectRepository = $projectRepository;
        $this->serializer = $serializer;
    }

    #[Route('/api/projects/{id}', name: 'api_projects_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(string $id): Response
    {
        $project = $this->projectRepository->findProjectById($id);

        if (!$project) {
            return $this->json([
                'message' => 'Aucun projet trouvé',
            ], 404);
        }

        $context = (new ObjectNormalizerContextBuilder())
            ->withContext([
                'groups' => ['project_read'],
                DateTimeNormalizer::FORMAT_KEY => 'd/m/Y',
            ])
            ->toArray();

        $serializedProject = $this->serializer->serialize($project, 'json', $context);

        return new JsonResponse($serializedProject, Response::HTTP_OK, [
            'Content-Type' => 'application/json'
        ], true);
    }

    #[Route('/api/projects/{slug}', name: 'api_projects_show_slug', methods: ['GET'], requirements: ['slug' => '[a-z0-9\-]+'])] //phpcs:ignore
    public function showSlug(string $slug): Response
    {
        $project = $this->projectRepository->findProjectBySlug($slug);

        if (!$project) {
            return $this->json([
                'message' => 'Aucun projet trouvé',
            ], 404);
        }

        $context = (new ObjectNormalizerContextBuilder())
            ->withContext([
                'groups' => ['project_read'],
                DateTimeNormalizer::FORMAT_KEY => 'd/m/Y',
            ])
            ->toArray();

        $serializedProject = $this->serializer->serialize($project, 'json', $context);

        return new JsonResponse($serializedProject, Response::HTTP_OK, [
            'Content-Type' => 'application/json'
        ], true);
    }
}
