<?php

namespace App\Controller\Admin\Projects;

use App\Entity\Images;
use DateTimeImmutable;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/admin/projects', name: 'admin_projects_')]
class EditProjectsController extends AbstractController
{
    #[Route('/edit/{id}', name: 'update', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        EntityManagerInterface $em,
        ProjectRepository $projects,
        SluggerInterface $slugger,
        CacheInterface $cache,
        CacheItemPoolInterface $cacheItemPool,
        string $id
    ): Response {

        $project = $projects->findOneBy(['id' => $id, 'deleted' => false]);

        if (!$project) {
            throw $this->createNotFoundException('Aucun projet trouvé');
        }

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        $project = $form->getData();

        $img = new Images();

        $data = [];

        if ($form->isSubmitted() && !$form->isValid()) {
            $errors = $form->getErrors(true, true);
            foreach ($errors as $error) {
                if ($error instanceof FormError) {
                    $data[] = $error->getMessage();
                }
            }
        }

        // https://symfony.com/doc/current/controller/upload_file.html
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile $imageFile */
            $uploadedFile = $form->get('imageFile')->getData();

            if ($uploadedFile !== null) {
                 /** @var String */
                $destination = $this->getParameter('image_project_directory');
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();
                try {
                    $uploadedFile->move(
                        $destination,
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                    $this->addFlash('danger', 'une erreur est survenue lors de l\'upload de l\'image');
                }
                $project->setImageName($newFilename);
                // $project->setImageFile($uploadedFile);
            }

            /** @var String */
            $destination2 = $this->getParameter('images_directory');
            $images = $form->get('images')->getData();
            if ($images !== null) {
                foreach ($images as $image) {
                    $fichier = md5(uniqid()) . '.' . $image->guessExtension();
                    $image->move(
                        $destination2,
                        $fichier
                    );
                    $img = new Images();
                    $img->setName($fichier);
                    $img->setPath($destination2 . '/' . $fichier);
                    $img->setProject($project);
                    $em->persist($img);
                    $em->flush();
                    $project->addImage($img);
                }
            }

            $backendLanguages = $form->get('backendLanguages')->getData();
            foreach ($backendLanguages as $backendLanguage) {
                if (!$project->getBackendLanguages()->contains($backendLanguage)) {
                    $project->removeBackendLanguage($backendLanguage);
                } else {
                    $project->addBackendLanguage($backendLanguage);
                }
            }

            $frontendLanguages = $form->get('frontendLanguages')->getData();
            foreach ($frontendLanguages as $frontendLanguage) {
                if (!$project->getFrontendLanguages()->contains($frontendLanguage)) {
                    $project->removeFrontendLanguage($frontendLanguage);
                } else {
                    $project->addFrontendLanguage($frontendLanguage);
                }
            }

            $tools = $form->get('tools')->getData();
            foreach ($tools as $tool) {
                if (!$project->getTools()->contains($tool)) {
                    $project->removeTool($tool);
                } else {
                    $project->addTool($tool);
                }
            }

            $project->setStatus($form->get('status')->getData());
            $project->setSlug($slugger->slug($project->getName())->lower());
            $project->setUpdatedAt(new DateTimeImmutable());

            $em->persist($project);
            $em->flush();

            $cacheItemPool->deleteItem('api_projects');

            $cache->delete('projects_list');
            $cache->delete('projects_list_archived');
            $cache->delete('backendLanguages_list');
            $cache->delete('frontendLanguages_list');
            $cache->delete('tools_list');

            $this->addFlash('success', 'Le projet ' . $project->getName() . ' a été modifié avec succès');
            $currentView = $request->cookies->get('currentView');
            return $this->redirectToRoute('admin_projects_index', ['currentView' => $currentView]);
        }

        return $this->render('admin/projects/edit.html.twig', [
          'form' => $form->createView(),
          'project' => $project,
        ]);
    }

    #[Route('/{id}/delete-thumbnail/{imageName}', name: 'delete_thumbnail', methods: ['POST', 'DELETE'])]
    public function deleteImage(
        EntityManagerInterface $em,
        ProjectRepository $projects,
        CacheInterface $cache,
        string $id,
        string $imageName,
    ): Response {

        $project = $projects->findOneBy(['id' => $id, 'imageName' => $imageName,]);

        if (!$project) {
            throw $this->createNotFoundException('Aucun projet trouvé');
        }

        $project->setImageName(null);
        $project->setImageFile(null);

        $em->persist($project);
        $em->flush();

        $cache->delete('projects_list');

        return new Response(null, 204);
    }
}
