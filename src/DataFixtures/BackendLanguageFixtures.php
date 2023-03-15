<?php

namespace App\DataFixtures;

use App\Entity\BackendLanguage;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class BackendLanguageFixtures extends Fixture
{
    public function __construct(private SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $this->createLanguage($manager, 'PHP');
        $this->createLanguage($manager, 'JavaScript');
        $this->createLanguage($manager, 'NodeJs');
        $this->createLanguage($manager, 'ExpressJs');
        $this->createLanguage($manager, 'Python');
        $this->createLanguage($manager, 'Ruby');
        $this->createLanguage($manager, 'Java');
        $this->createLanguage($manager, 'C#');
        $this->createLanguage($manager, 'Perl');
        $this->createLanguage($manager, 'C++');
        $this->createLanguage($manager, 'Kotlin');
        $this->createLanguage($manager, 'Laravel');
        $this->createLanguage($manager, 'Django');
        $this->createLanguage($manager, 'Symfony');
        $this->createLanguage($manager, 'SQL');

        $manager->flush();
    }

    public function createLanguage(ObjectManager $manager, string $name): BackendLanguage
    {
        $language = new BackendLanguage();
        $language->setName($name);
        $language->setSlug($this->slugger->slug($name)->lower());
        $manager->persist($language);
        return $language;
    }
}
