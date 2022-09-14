<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectApiController extends AbstractController
{
    #[Route('/api/project', name: 'app_api_project')]
    public function index(): Response
    {
        return $this->render('api/project/index.html.twig', [
            'controller_name' => 'ProjectController',
        ]);
    }
}
