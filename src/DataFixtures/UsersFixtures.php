<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UsersFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder,
        private SluggerInterface $slugger
    ) {
        $this->passwordEncoder = $passwordEncoder;
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('noemie@stinguette.fr');
        $admin->setLastname('Stinguette');
        $admin->setFirstname('Noemie');
        $admin->setPassword($this->passwordEncoder->hashPassword($admin, 'admin123'));
        $admin->setSlug($this->slugger->slug((string)$admin->getFirstname())->lower() . '-' . $this->slugger->slug((string)$admin->getLastname())->lower());//phpcs:ignore
        $admin->setRoles(["ROLE_ADMIN"]);
        $manager->persist($admin);

        // je crée un utilisateur de test
        $newUser = new User();
        $newUser->setEmail('joe@stinguette.fr');
        $newUser->setLastname('Stinguette');
        $newUser->setFirstname('Joe');
        $newUser->setPassword($this->passwordEncoder->hashPassword($newUser, 'user123'));
        $newUser->setSlug($this->slugger->slug((string)$newUser->getFirstname())->lower() . '-' . $this->slugger->slug((string)$newUser->getLastname())->lower());//phpcs:ignore
        $newUser->setRoles(["ROLE_USER"]);
        $manager->persist($newUser);

        $faker = Factory::create('fr_FR');

        for ($usr = 1; $usr <= 10; $usr++) {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setLastname($faker->lastName);
            $user->setFirstname($faker->firstName);
            $user->setPassword($this->passwordEncoder->hashPassword($user, 'secret'));
            $user->setSlug($this->slugger->slug((string)$user->getFirstname())->lower() . '-' . $this->slugger->slug((string)$user->getLastname())->lower());//phpcs:ignore
            $user->setRoles($faker->randomElement([["ROLE_USER"], ["ROLE_ADMIN"]]));
            $user->setCountSession($faker->numberBetween(0, 100));
            $user->setLastLogin($faker->dateTimeBetween('-1 years', 'now'));
            // dump($user);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
