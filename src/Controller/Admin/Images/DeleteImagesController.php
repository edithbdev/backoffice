<?php

namespace App\Controller\Admin\Images;

use App\Repository\ImagesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/images', name: 'admin_images_')]
class DeleteImagesController extends AbstractController
{
    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function delete(
        EntityManagerInterface $em,
        ImagesRepository $images,
        string $id,
        CacheInterface $cache
    ): Response {
        $image = $images->findOneBy(['id' => $id]);
        $project = $image ? $image->getProject() : null;
        if ($image !== null && $project !== null) {
            $image->setDeleted(true);

            $fileDirection = "uploads/images/" . (string)$image->getName();
            if (file_exists($fileDirection) && is_file($fileDirection)) {
                unlink($fileDirection);
                $project->removeImage($image);
            }

            $em->persist($project);
            $em->persist($image);
            $em->flush();
            $cache->delete('projects_list');
            $cache->delete('projects_list_' . $project->getId());
            $this->addFlash('success', 'L\'image a bien été supprimée');
            return new Response(null, 204);
        } else {
            $this->addFlash('danger', 'Une erreur est survenue');
            return $this->redirectToRoute('admin_projects_index');
        }
    }
}
