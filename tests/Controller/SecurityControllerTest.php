<?php

namespace App\Tests\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityControllerTest extends WebTestCase
{
    public function testHomePage(): void
    {
        $client = static::createClient();

        $client->request('GET', '/');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertResponseRedirects('/login');
    }

    public function testLoginPage(): void
    {
        $client = static::createClient();

        $client->request('GET', '/login');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h2', 'Se connecter');
    }

    public function testHomePageWithLogin(): void
    {
        $client = static::createClient();

        $client->request('GET', '/login');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->submitForm('Se connecter', [
            'email' => 'noemie@stinguette.fr',
            'password' => 'admin123',
        ]);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertResponseRedirects('/');

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('h1', 'Bienvenue Noemie Stinguette');
    }

    public function testHomePageWithWrongCredentials(): void
    {
        $client = static::createClient();

        $client->request('GET', '/login');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->submitForm('Se connecter', [
            'email' => 'noemie@stinguette.fr',
            'password' => 'wrongpassword',
        ]);

        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertResponseRedirects('/login');

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('.alert-danger', 'Identifiants invalides.');
    }

    public function testLogoutPage(): void
    {
        $client = static::createClient();

        $client->request('GET', '/logout');

        $this->assertEquals(302, $client->getResponse()->getStatusCode());

        $client->followRedirect();

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertSelectorTextContains('h2', 'Se connecter');
    }

    public function testForgotPasswordPage(): void
    {
        $client = static::createClient();

        $client->request('GET', '/login');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->clickLink('Mot de passe oublié ?');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertSelectorTextContains('h3', 'Mot de passe oublié');

        $client->submitForm('Changer de mot de passe', [
            'reset_password_request[email]' => 'noemie@stinguette.fr',
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertSelectorTextContains('.alert-success', 'Un email de réinitialisation de mot de passe vous a été envoyé'); //phpcs:ignore
    }

    public function testForgotPasswordPageWithWrongEmail(): void
    {
        $client = static::createClient();

        $client->request('GET', '/login');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->clickLink('Mot de passe oublié ?');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertSelectorTextContains('h3', 'Mot de passe oublié');

        $client->submitForm('Changer de mot de passe', [
            'reset_password_request[email]' => 'noe@stinguette.fr',
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertSelectorTextContains('.alert-danger', 'Aucun compte n\'est associé à cette adresse email');
    }

    public function testResetPassword(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);
        $passwordEncoder = $client->getContainer()->get(UserPasswordHasherInterface::class);
        $userRepository = $entityManager->getRepository(User::class);

        // création d'un utilisateur avec un token de réinitialisation
        $user = new User();
        $user->setEmail('testReset@example.com');
        $user->setPassword($passwordEncoder->hashPassword($user, 'testReset_password'));
        $user->setLastname('testReset_lastname');
        $user->setFirstname('testReset_firstname');
        $user->setRoles(['ROLE_USER']);
        $user->setResetToken('testReset_token');
        $entityManager->persist($user);
        $entityManager->flush();

        // accès à la page de réinitialisation de mot de passe
        $client->request('GET', '/forgotten-password/testReset_token');
        $this->assertResponseIsSuccessful();

        // soumission du formulaire de réinitialisation de mot de passe
        $client->submitForm('Changer de mot de passe', [
            'reset_password[password][first]' => 'new_password',
            'reset_password[password][second]' => 'new_password',
        ]);
        $this->assertSelectorTextContains('.alert-success', 'Votre mot de passe a bien été modifié, retourner à la page de connexion Se connecter'); //phpcs:ignore

        // On vérifie que le mot de passe a bien été modifié et que le token de réinitialisation a bien été supprimé
        $user = $userRepository->findOneBy(['email' => 'testReset@example.com']);
        $this->assertTrue($passwordEncoder->isPasswordValid($user, 'new_password'));
        $this->assertEmpty($user->getResetToken());
    }

    public function testSendVerificationEmail(): void
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);
        $passwordEncoder = $client->getContainer()->get(UserPasswordHasherInterface::class);

        $user = new User();
        $user->setEmail('testVerifEmail@exemple.com');
        $user->setPassword($passwordEncoder->hashPassword($user, 'test_password'));
        $user->setLastname('testVerifEmail_lastname');
        $user->setRoles(['ROLE_USER']);
        $user->setFirstname('testVerifEmail_firstname');
        $user->setResetToken('testVerifEmail_token');
        $entityManager->persist($user);
        $entityManager->flush();

        $client->request('GET', '/send-verification-email');

        $client->submitForm('Envoyer', [
            'verif_email_request[email]' => 'testVerifEmail@exemple.com',
        ]);

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $this->assertSelectorTextContains('.alert-success', 'Un email de vérification de votre adresse email vous a été envoyé'); //phpcs:ignore

        $this->assertEmailCount(1);
    }
}
