<?php

namespace App\DataFixtures;

use App\Entity\Language;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class LanguagesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $language = new Language();
        // $language->setName('Symfony'); // Symfony
        // $manager->persist($language);

        $this->createLanguage($manager, 'PHP');
        $this->createLanguage($manager, 'JavaScript');
        $this->createLanguage($manager, 'Python');
        $this->createLanguage($manager, 'HTML');
        $this->createLanguage($manager, 'CSS');
        $this->createLanguage($manager, 'MYSQL');
        $this->createLanguage($manager, 'React');
        $this->createLanguage($manager, 'Angular');
        $this->createLanguage($manager, 'GraphQL');
        $this->createLanguage($manager, 'TypeScript');
        $this->createLanguage($manager, 'Laravel');
        $this->createLanguage($manager, 'Symfony');
        $this->createLanguage($manager, 'Vue.js');
        $this->createLanguage($manager, 'Sass');
        $this->createLanguage($manager, 'PostgreSQL');
        $this->createLanguage($manager, 'Express');
        $this->createLanguage($manager, 'Node.js');
        $this->createLanguage($manager, 'Docker');
        $this->createLanguage($manager, 'Bootstrap');
        $this->createLanguage($manager, 'Material-UI');

        $manager->flush();
    }

    public function createLanguage(ObjectManager $manager, string $name): Language
    {
        $language = new Language();
        $language->setName($name);
        $manager->persist($language);
        return $language;
    }
}
