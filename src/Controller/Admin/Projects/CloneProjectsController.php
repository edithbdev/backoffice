<?php

namespace App\Controller\Admin\Projects;

use DateTimeImmutable;
use App\Entity\Enum\Status;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/projects', name: 'admin_projects_')]
class CloneProjectsController extends AbstractController
{
    #[Route('/clone/{id}', name: 'clone', requirements: ['id' => '\d+'], methods: ['POST', 'GET'])]
    public function clone(
        ProjectRepository $projects,
        EntityManagerInterface $em,
        string $id
    ): Response {
        $project = $projects->findBy(['id' => $id, 'deleted' => false])[0];

        $clone = clone $project;
        if ($project !== null) {
            $clone->setName($project->getName() . ' (clone)');
            $clone->setSlug($project->getSlug() . '-clone');
            $clone->setStatus(Status::Draft);
            $clone->setCreatedAt(new DateTimeImmutable());

            $em->persist($clone);
            $em->flush();

            $this->addFlash('success', 'Le projet ' . $project->getName() . ' a été cloné');
            return $this->redirectToRoute('admin_projects_index');
        }
    }
}
