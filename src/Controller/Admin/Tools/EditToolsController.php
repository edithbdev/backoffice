<?php

namespace App\Controller\Admin\Tools;

use App\Form\ToolType;
use DateTimeImmutable;
use App\Repository\ToolRepository;
use App\Repository\ProjectRepository;
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
class EditToolsController extends AbstractController
{
    #[Route('/edit/{id}', name: 'update', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(
        ToolRepository $tools,
        ProjectRepository $projectsRepository,
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        string $id,
        CacheInterface $cache,
        CacheItemPoolInterface $cacheItemPool
    ): Response {
        $tool = $tools->findOneBy(
            ['id' => $id, 'deleted' => false]
        );

        if (!$tool) {
            throw $this->createNotFoundException('Aucun outil trouvé');
        }

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
            if ($form->get('name')->getData()) {
                $tool->setSlug($slugger->slug($tool->getName())->lower());
            }

            $projects = $projectsRepository->findBy(['deleted' => false]);
            foreach ($projects as $project) {
                if ($form->get('projects')->getData()->contains($project)) {
                    $project->addTool($tool);
                } else {
                    $project->removeTool($tool);
                }
            }

            $tool->setUpdatedAt(new DateTimeImmutable());
            $em->persist($tool);
            $em->flush();

            $cacheItemPool->deleteItem('api_projects');

            $cache->delete('projects_list');
            $cache->delete('tools_list');

            $this->addFlash('success', 'L\'outil a bien été modifié');
            return $this->redirectToRoute('admin_tools_index');
        }
        return $this->render('admin/tools/edit.html.twig', [
            'form' => $form->createView(),
            'tool' => $tool,
        ]);
    }
}
