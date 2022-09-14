<?php

// strict_types=1 indicates that all PHP code must be run in strict mode.
declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

// final because we don't want to extend this class
// #[Route('/api/projects/random', name: 'app_api_project_random_collection')]
final class RandomProjectController extends AbstractController
{
    public function __construct(private ProjectRepository $projectRepository)
    {
    }

// invoke permet d'appeler une méthode d'un objet
    public function __invoke(): Project
    {
        return $this->projectRepository->getRandomProject();
    }

}
