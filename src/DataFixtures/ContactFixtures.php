<?php

namespace App\DataFixtures;

use App\Entity\Contact;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker;

class ContactFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for ($cont = 1; $cont <= 15; $cont++) {
            $contact = new Contact();
            $contact->setFirstname($faker->firstName());
            $contact->setLastname($faker->lastName());
            $contact->setEmail($faker->email());
            $contact->setSubject('Demande n°' . (string)($cont + 1));
            $contact->setMessage($faker->text());
            $manager->persist($contact);
        }

        $manager->flush();
    }
}
