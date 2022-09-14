<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;

class UsersFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordEncoder,
        private SluggerInterface $slugger
    )
    {
        $this->password = $passwordEncoder;
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = new User();
        $admin->setEmail('admin@backoffice.fr');
        $admin->setLastname('Bredon');
        $admin->setFirstname('Edith');
        $admin->setPhone('0612345678');
        $admin->setSlug($this->slugger->slug($admin->getFirstname())->lower());
        $admin->setPassword($this->passwordEncoder->hashPassword($admin, 'admin'));
        $admin->setRoles(["ROLE_ADMIN"]);
        $manager->persist($admin);

        // faker pour générer des données aléatoires pour créer des utilisateurs
        $faker = Faker\Factory::create('fr_FR');

        for ($usr = 1; $usr <=8; $usr++) {
            $user = new User();
            $user->setEmail($faker->email);
            $user->setLastname($faker->lastname);
            $user->setFirstname($faker->firstname);
            $user->setPhone($faker->phoneNumber);
            $user->setSlug($this->slugger->slug($user->getFirstname())->lower());
            $user->setPassword($this->passwordEncoder->hashPassword($user, 'secret'));
            // dump($user);
            $manager->persist($user);
        }

        $manager->flush();
    }
}
