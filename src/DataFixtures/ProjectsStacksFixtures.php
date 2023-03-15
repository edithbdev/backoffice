<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\Tool;
use App\Entity\BackendLanguage;
use App\Entity\FrontendLanguage;
use App\DataFixtures\ProjectsFixtures;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\DataFixtures\ToolFixtures;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProjectsStacksFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $projects = $manager->getRepository(Project::class)->findAll();
        $frontendLanguages = $manager->getRepository(FrontendLanguage::class)->findAll();
        $backendLanguages = $manager->getRepository(BackendLanguage::class)->findAll();
        $tools = $manager->getRepository(Tool::class)->findAll();

        foreach ($projects as $project) {
            $randomFrontLanguages = rand(0, count($frontendLanguages) - 1);
            $randomBackLanguages = rand(0, count($backendLanguages) - 1);
            $randomTools = rand(0, count($tools) - 1);
            // on indique -1 pour ne pas avoir un index out of bound
            $project->addFrontendLanguage($frontendLanguages[$randomFrontLanguages]);
            $project->addBackendLanguage($backendLanguages[$randomBackLanguages]);
            $project->addTool($tools[$randomTools]);


            foreach ($frontendLanguages as $frontendLanguage) {
                $randomProjects = rand(0, count($projects) - 1);
                $frontendLanguage->addProject($projects[$randomProjects]);
            }

            foreach ($backendLanguages as $backendLanguage) {
                $randomProjects = rand(0, count($projects) - 1);
                $backendLanguage->addProject($projects[$randomProjects]);
            }

            foreach ($tools as $tool) {
                $randomProjects = rand(0, count($projects) - 1);
                $tool->addProject($projects[$randomProjects]);
            }

            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            ProjectsFixtures::class,
            BackendLanguageFixtures::class,
            FrontendLanguageFixtures::class,
            ToolFixtures::class,
        ];
    }
}
