<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\Language;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ProjectsLanguagesFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // ajout de fixtures dans la table de liaison entre projet et langage
        // random pour attribuer un langage à un projet et inversement
        $projects = $manager->getRepository(Project::class)->findAll();
        $languages = $manager->getRepository(Language::class)->findAll();

        foreach ($projects as $project) {
             $random = rand(0, count($languages) -1); // on indique -1 pour ne pas avoir un index out of bound
             $project->addLanguage($languages[$random]);
         }

         foreach ($languages as $language) {
             $random = rand(0, count($projects) -1);
             $language->addProject($projects[$random]);
         }

        $manager->flush();
    }
}
