<?php

namespace App\Controller\Admin\Projects;

use App\Entity\Images;
use DateTimeImmutable;
use App\Entity\Project;
use App\Form\ProjectType;
use App\Entity\Enum\Status;
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
use Psr\Cache\CacheItemPoolInterface;

#[Route('/admin/projects', name: 'admin_projects_')]
class AddProjectsController extends AbstractController
{
    #[Route('/add', name: 'create', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger,
        CacheInterface $cache,
        CacheItemPoolInterface $cacheItemPool
    ): Response {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

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
            $project->setSlug($slugger->slug($project->getName() ? $project->getName() : '')->lower());

            $frontendLanguages = $form->get('frontendLanguages')->getData();
            foreach ($frontendLanguages as $frontendLanguage) {
                $project->addFrontendLanguage($frontendLanguage);
            }

            $backendLanguages = $form->get('backendLanguages')->getData();
            foreach ($backendLanguages as $backendLanguage) {
                $project->addBackendLanguage($backendLanguage);
            }

            $tools = $form->get('tools')->getData();
            foreach ($tools as $technique) {
                $project->addTool($technique);
            }

            /** @var UploadedFile $uploadedFile $imageFile */
            $uploadedFile = $form->get('imageFile')->getData();

            if ($uploadedFile !== null) {
                /** @var String */
                $destination = $this->getParameter('image_project_directory');
                $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
                // ceci est nécessaire pour inclure en toute sécurité le nom du fichier dans l'URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $uploadedFile->guessExtension();

                //déplace le fichier dans le répertoire où les images sont stockées
                try {
                    $uploadedFile->move(
                        $destination,
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                $project->setImageName($newFilename);
            }

            $images = $form->get('images')->getData();

            /** @var String */
            $imagesDirectory = $this->getParameter('images_directory');

            foreach ($images as $image) {
                // On génère un nouveau nom de fichier
                $fichier = md5(uniqid()) . '.' . $image->guessExtension();

                // On copie le fichier dans le dossier uploads
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );

                /** @var String */
                $destination = $imagesDirectory[0] . '/' . $fichier;

                // On crée l'image dans la base de données
                $img = new Images();
                $img->setName($fichier);
                $img->setPath(
                    $destination
                );
                $img->setProject($project);
                $project->addImage($img);
            }

            $project->setCreatedAt(new DateTimeImmutable());
            $project->setStatus($form->get('status')->getData() ?? Status::Draft);

            $em->persist($project);
            $em->flush();

            // On invalide le cache pour la liste des projets anvoyée à l'API
            $cacheItemPool->deleteItem('api_projects');

            $cache->delete('projects_list');
            $cache->delete('backendLanguages_list');
            $cache->delete('frontendLanguages_list');
            $cache->delete('tools_list');

            $this->addFlash('success', 'Le projet ' . $project->getName() . ' a bien été ajouté !');
            return $this->redirectToRoute('admin_projects_index');
        }

        return $this->render('admin/projects/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
