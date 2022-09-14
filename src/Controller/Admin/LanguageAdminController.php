<?php

namespace App\Controller\Admin;

use App\Entity\Language;
use App\Form\LanguageType;
use App\Repository\LanguageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/languages', name: 'admin_languages_')]
class LanguageAdminController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(LanguageRepository $languages): Response
    {
        return $this->render('admin/language/index.html.twig', [
            'languages' => $languages->findAll(),
        ]);
    }

    #[Route('/create', name: 'create')]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        LanguageRepository $languages): Response
    {
        $language = new Language();
        $form = $this->createForm(LanguageType::class, $language);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($language);
            $em->flush();
            $this->addFlash('success', 'Language created successfully');
            return $this->redirectToRoute('admin_languages_index');
        }

        return $this->render('admin/language/create.html.twig', [
            'form' => $form->createView(),
            // 'languages' => count($languages->findAll()) > 0
            // ? $languages->findAll() : null,
            'languages' => $languages->findAll(),
        ]);
    }
}
