<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{
    #[Route('/', name: 'homepage', methods: ['GET'], priority: 1)]
    // priority: 1 signifie que cette route sera prioritaire sur les autres routes
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
        ]);
    }
}
