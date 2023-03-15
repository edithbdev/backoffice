<?php

namespace App\Controller\Api\Projects;

use App\Repository\ProjectRepository;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Context\Normalizer\ObjectNormalizerContextBuilder;

class IndexApiProjectsController extends AbstractController
{
    private ProjectRepository $projectRepository;
    private SerializerInterface $serializer;
    private CacheItemPoolInterface $cache;

    public function __construct(
        ProjectRepository $projectRepository,
        SerializerInterface $serializer,
        CacheItemPoolInterface $cache
    ) {
        $this->projectRepository = $projectRepository;
        $this->serializer = $serializer;
        $this->cache = $cache;
    }

    #[Route('/api/projects', name: 'api_projects', methods: ['GET'])]
    public function index(): Response
    {
        // On définit la clé de cache
        $cacheKey = 'api_projects';

        // On récupère les données en cache
        $cachedData = $this->cache->getItem($cacheKey);

        // On vérifie si les données sont en cache
        if (!$cachedData->isHit()) {
            $projects = $this->projectRepository->findAllProjects();

            if (!$projects) {
                return new JsonResponse(['message' => 'Aucun projet trouvé'], Response::HTTP_NOT_FOUND);
            }

            $context = (new ObjectNormalizerContextBuilder())
                ->withContext([
                    'groups' => ['project_list'],
                    DateTimeNormalizer::FORMAT_KEY => 'd/m/Y',
                ])
                ->toArray();

            $serializedProjects = $this->serializer->serialize(["results" => $projects], 'json', $context);

            // On sauvegarde les données en cache
            $cachedData->set($serializedProjects);

            // On définit la durée de vie du cache
            $cachedData->expiresAfter(1800); // 30 minutes
            $this->cache->save($cachedData);
        } else {
            $serializedProjects = $cachedData->get();
        }

        return new JsonResponse($serializedProjects, Response::HTTP_OK, [
            'Content-Type' => 'application/json'
        ], true);
    }
}
