<?php

namespace App\Tests\Controller;

use App\Entity\Project;
use App\Entity\Enum\Status;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ProjectControllerTest extends WebTestCase
{
    private const ADMIN_EMAIL = 'noemie@stinguette.fr';
    private const ADMIN_PASSWORD = 'admin123';
    private const USER_EMAIL = 'joe@stinguette.fr';
    private const USER_PASSWORD = 'user123';


    private $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    private function loginAsAdmin(): void
    {
        $this->client->request('GET', '/login');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->client->submitForm('Se connecter', [
            'email' => self::ADMIN_EMAIL,
            'password' => self::ADMIN_PASSWORD,
        ]);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertResponseRedirects('/');

        $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h1', 'Bienvenue Noemie Stinguette');
    }

    private function loginAsUser(): void
    {
        $this->client->request('GET', '/login');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->client->submitForm('Se connecter', [
            'email' => self::USER_EMAIL,
            'password' => self::USER_PASSWORD,
        ]);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertResponseRedirects('/');

        $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h1', 'Bienvenue Joe Stinguette');
    }

    public function testIndexProjectsIfNotLogged(): void
    {
        $this->client->request('GET', '/admin/projects/');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertResponseRedirects('/login');
    }

    public function testIndexProjectsIfLoggedAsAdmin(): void
    {
        $this->loginAsAdmin();

        $this->client->request('GET', '/admin/projects/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h1', 'Liste des projets');
    }

    public function testIndexProjectsIfLoggedAsUser(): void
    {
        $this->loginAsUser();

        $this->client->request('GET', '/admin/projects/');
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function testAddProjectsIfNotLogged(): void
    {
        $this->client->request('GET', '/admin/projects/add');

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertResponseRedirects('/login');
    }

    public function testAddProjectsIfLoggedAsAdmin(): void
    {
        $this->loginAsAdmin();

        $this->client->request('GET', '/admin/projects/add');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h1', 'Ajouter un projet');

        $form = $this->client->getCrawler()->selectButton('Ajouter')->form([
            'project[name]' => 'Projet de test',
            'project[description]' => 'Description du projet de test',
            'project[projectLink]' => 'https://www.google.fr',
            'project[githubLink]' => 'https://github.com',
        ]);

        $this->client->submit($form);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->assertResponseRedirects('/admin/projects/');

        $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h1', 'Liste des projets');
        $this->assertSelectorTextContains('div.alert.alert-success', 'Le projet Projet de test a bien été ajouté !');
    }

    public function testEditProjectsIfLoggedAsAdmin(): void
    {
        $this->loginAsAdmin();

        $this->client->request('GET', '/admin/projects/edit/16');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h1', 'Modification du projet : Projet de test');


        $form = $this->client->getCrawler()->selectButton('Modifier')->form([
            'project[name]' => 'Projet de test modifié',
            'project[description]' => 'Description du projet de test modifié',
            'project[projectLink]' => 'https://www.google.fr',
            'project[githubLink]' => 'https://github.com',
        ]);

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/projects/');

        $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h1', 'Liste des projets');
        $this->assertSelectorTextContains('div.alert.alert-success', 'Le projet Projet de test modifié a été modifié avec succès'); //phpcs:ignore
    }

    public function testDeleteProjectsIfLoggedAsAdmin(): void
    {
        $this->loginAsAdmin();

        $project = $this->client->getContainer()->get('doctrine')->getRepository(Project::class)->findOneBy(['name' => 'Projet de test modifié']);//phpcs:ignore
        $projectId = $project->getId();

        $this->client->request('GET', '/admin/projects/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Liste des projets');

        $deleteButton = $this->client->getCrawler()->filter('button[data-bs-target="#deleteConfirmationModal' . $projectId . '"]');//phpcs:ignore
        $this->assertResponseIsSuccessful();

        $form = $deleteButton->form();

        $this->client->submit($form);

        $this->assertResponseRedirects('/admin/projects/');
        $this->client->followRedirect();

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $this->assertSelectorTextContains('h1', 'Liste des projets');

        $this->assertSelectorTextContains('div.alert.alert-success', 'Le projet a bien été supprimé');

        $entityManager = $this->client->getContainer()->get('doctrine.orm.entity_manager');
        $deletedProject = $entityManager->getRepository(Project::class)->find($projectId);

        $this->assertEquals(Status::Archived, $deletedProject->getStatus());

        $this->assertEquals(true, $deletedProject->getDeleted());
    }
}
