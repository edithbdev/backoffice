<?php

namespace App\DataFixtures;

use App\Entity\FrontendLanguage;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class FrontendLanguageFixtures extends Fixture
{
    public function __construct(private SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $this->createLanguage($manager, 'React');
        $this->createLanguage($manager, 'JavaScript');
        $this->createLanguage($manager, 'NextJs');
        $this->createLanguage($manager, 'HTML');
        $this->createLanguage($manager, 'CSS');
        $this->createLanguage($manager, 'Swift');
        $this->createLanguage($manager, 'Angular');
        $this->createLanguage($manager, 'Flutter');
        $this->createLanguage($manager, 'TypeScript');
        $this->createLanguage($manager, 'Backbone');
        $this->createLanguage($manager, 'Sass');
        $this->createLanguage($manager, 'PostgreSQL');
        $this->createLanguage($manager, 'Tailwindcss');
        $this->createLanguage($manager, 'JQuery');
        $this->createLanguage($manager, 'Vue');

        $manager->flush();
    }

    public function createLanguage(ObjectManager $manager, string $name): FrontendLanguage
    {
        $language = new FrontendLanguage();
        $language->setName($name);
        $language->setSlug($this->slugger->slug($name)->lower());
        $manager->persist($language);
        return $language;
    }
}
