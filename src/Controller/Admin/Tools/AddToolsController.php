<?php

namespace App\Controller\Admin\Tools;

use App\Entity\Tool;
use App\Form\ToolType;
use DateTimeImmutable;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/tools', name: 'admin_tools_')]
class AddToolsController extends AbstractController
{
    #[Route('/add', name: 'create', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        CacheInterface $cache,
        CacheItemPoolInterface $cacheItemPool
    ): Response {
        $tool = new Tool();
        $form = $this->createForm(ToolType::class, $tool);
        $form->handleRequest($request);

        $tool = $form->getData();

        $data = [];

        if ($form->isSubmitted() && !$form->isValid()) {
            $errors = $form->getErrors(true, true);
            foreach ($errors as $error) {
                if ($error instanceof FormError) {
                    $data[] = $error->getMessage();
                }
            }
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $tool->setSlug($slugger->slug($tool->getName() ? $tool->getName() : '')->lower());

            $projects = [];
            foreach ($tool->getProjects() as $project) {
                $project->addTool($tool);
                $projects[] = $project;
            }

            $tool->setCreatedAt(new DateTimeImmutable());
            $em->persist($tool);
            $em->flush();

            $cacheItemPool->deleteItem('api_projects');

            $cache->delete('projects_list');
            $cache->delete('tools_list');

            $this->addFlash('success', 'L\'outil ' . $tool->getName() . ' a bien été ajouté !');
            return $this->redirectToRoute('admin_tools_index');
        }

        return $this->render('admin/tools/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
