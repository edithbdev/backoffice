<?php

namespace App\Tests\Controller;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use App\Entity\Enum\Status;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProjectControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private ProjectRepository $projectRepository;
    private string $path = '/admin/projects/';

    private const ADMIN_EMAIL = 'noemie@stinguette.fr';
    private const ADMIN_PASSWORD = 'admin123';
    private const USER_EMAIL = 'joe@stinguette.fr';
    private const USER_PASSWORD = 'user123';

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->projectRepository = static::getContainer()->get('doctrine')->getRepository(Project::class);

        foreach ($this->projectRepository->findAll() as $project) {
            $this->projectRepository->remove($project);
        }
    }

    protected function loginAsAdmin(): void
    {
        $this->client->request('GET', '/login');

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Se connecter', [
            'email' => self::ADMIN_EMAIL,
            'password' => self::ADMIN_PASSWORD,
        ]);

        self::assertResponseStatusCodeSame(302);
        self::assertResponseRedirects('/');

        $this->client->followRedirect();

        self::assertResponseStatusCodeSame(200);
        self::assertSelectorTextContains('h1', 'Bienvenue Noemie Stinguette');
    }

    protected function loginAsUser(): void
    {
        $this->client->request('GET', '/login');

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Se connecter', [
            'email' => self::USER_EMAIL,
            'password' => self::USER_PASSWORD,
        ]);

        self::assertResponseStatusCodeSame(302);
        self::assertResponseRedirects('/');

        $this->client->followRedirect();

        self::assertResponseStatusCodeSame(200);
        self::assertSelectorTextContains('h1', 'Bienvenue Joe Stinguette');
    }

    public function testIndexIfNotLogged(): void
    {
        $this->client->request('GET', $this->path);
        self::assertResponseStatusCodeSame(302);
        self::assertResponseRedirects('/login');
    }

    public function testIndexIfLoggedAsAdmin(): void
    {
        $this->loginAsAdmin();

        $this->client->request('GET', $this->path);
        self::assertResponseStatusCodeSame(200);
        self::assertSelectorTextContains('h1', 'Liste des projets');
    }

    public function testIndexIfLoggedAsUser(): void
    {
        $this->loginAsUser();

        $this->client->request('GET', $this->path);
        self::assertResponseStatusCodeSame(403);
    }

    public function testNewIfNotLogged(): void
    {
        $this->client->request('GET', sprintf('%sadd', $this->path));

        self::assertResponseStatusCodeSame(302);
        self::assertResponseRedirects('/login');
    }

    public function testNewIfLoggedAsAdmin(): void
    {
        $this->loginAsAdmin();

        $originalNumObjectsInRepository = count($this->projectRepository->findAll());

        $this->client->request('GET', sprintf('%sadd', $this->path));

        self::assertResponseStatusCodeSame(200);
        self::assertSelectorTextContains('h1', 'Ajouter un projet');

        $form = $this->client->getCrawler()->selectButton('Ajouter')->form([
            'project[name]' => 'Projet de test',
            'project[description]' => 'Description du projet de test',
            'project[projectLink]' => 'https://www.google.fr',
            'project[githubLink]' => 'https://github.com',
        ]);

        $this->client->submit($form);

        self::assertResponseStatusCodeSame(302);
        self::assertResponseRedirects('/admin/projects/');

        self::assertSame($originalNumObjectsInRepository + 1, count($this->projectRepository->findAll()));

        $this->client->followRedirect();

        self::assertResponseStatusCodeSame(200);
        self::assertSelectorTextContains('h1', 'Liste des projets');
        self::assertSelectorTextContains('div.alert.alert-success', 'Le projet Projet de test a bien été ajouté !');
    }

    public function testEditIfLoggedAsAdmin(): void
    {
        $this->loginAsAdmin();

        $this->client->request('GET', sprintf('%sedit/16', $this->path));
        self::assertResponseStatusCodeSame(200);
        self::assertSelectorTextContains('h1', 'Modification du projet : Projet de test');


        $form = $this->client->getCrawler()->selectButton('Modifier')->form([
            'project[name]' => 'Projet de test modifié',
            'project[description]' => 'Description du projet de test modifié',
            'project[projectLink]' => 'https://www.google.fr',
            'project[githubLink]' => 'https://github.com',
        ]);

        $this->client->submit($form);

        self::assertResponseRedirects('/admin/projects/');

        $this->client->followRedirect();

        self::assertResponseStatusCodeSame(200);
        self::assertSelectorTextContains('h1', 'Liste des projets');
        self::assertSelectorTextContains('div.alert.alert-success', 'Le projet Projet de test modifié a été modifié avec succès'); //phpcs:ignore
    }

    public function testDeleteProjectsIfLoggedAsAdmin(): void
    {
        $this->loginAsAdmin();

        $originalNumObjectsInRepository = count($this->projectRepository->findAll());

        $project = $this->client->getContainer()->get('doctrine')->getRepository(Project::class)->findOneBy(['name' => 'Projet de test modifié']); //phpcs:ignore
        $projectId = $project->getId();

        $this->client->request('GET', $this->path);

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Liste des projets');

        $deleteButton = $this->client->getCrawler()->filter('button[data-bs-target="#deleteConfirmationModal' . $projectId . '"]'); //phpcs:ignore
        self::assertResponseIsSuccessful();

        $form = $deleteButton->form();

        $this->client->submit($form);

        self::assertSame($originalNumObjectsInRepository, count($this->projectRepository->findAll()));

        self::assertResponseRedirects('/admin/projects/');
        $this->client->followRedirect();

        self::assertResponseStatusCodeSame(200);

        self::assertSelectorTextContains('h1', 'Liste des projets');

        self::assertSelectorTextContains('div.alert.alert-success', 'Le projet a bien été supprimé');

        $entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $deletedProject = $entityManager->getRepository(Project::class)->find($projectId);

        self::assertEquals(Status::Archived, $deletedProject->getStatus());

        self::assertEquals(true, $deletedProject->getDeleted());
    }
}
