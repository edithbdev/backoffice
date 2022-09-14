<?php

namespace App\DataFixtures;

use App\Entity\Project;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Faker;

class ProjectsFixtures extends Fixture
{
    public function __construct(private SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
    //     $randomImage = "https://picsum.photos/200.webp";
    //     $randomLink = "https://picsum.photos/400.webp";

    //     $this->createProject($manager, 'Backoffice', 'Backoffice for the company', $randomImage, $randomLink);
    //     $this->createProject($manager, 'Frontoffice', 'Frontoffice for the company', $randomImage, $randomLink);
    //     $this->createProject($manager, 'Website', 'Website for the company', $randomImage, $randomLink);
    //     $this->createProject($manager, 'Mobile', 'Mobile application for the company', $randomImage, $randomLink);
    //     $this->createProject($manager, 'Gyn&Co', 'Linking site between patient and doctor', $randomImage, $randomLink);
    //     $this->createProject($manager, 'La bonne affaire', 'Good deals website', $randomImage, $randomLink);

    //     $manager->flush();
    // }

    // public function createProject(
    //     ObjectManager $manager,
    //     string $name,
    //     string $description,
    //     string $image,
    //     string $link,
    //     ): Project
    // {
    //     $project = new Project();
    //     $project->setName($name);
    //     $project->setDescription($description);
    //     $project->setImage($image);
    //     $project->setLink($link);
    //     $project->setIsPublished((bool)rand(0,1));
    //     $project->setSlug($this->slugger->slug($project->getName())->lower());
    //     $manager->persist($project);
    //     return $project;
    // }

    $faker = Faker\Factory::create('fr_FR');

    $randomImage = "https://picsum.photos/200.webp";

    for ($proj = 1; $proj <= 10; $proj++) {
        $project = new Project();
        $project->setName($faker->text(15));
        $project->setDescription($faker->text());
        $project->setImage($randomImage);
        $project->setLink($faker->imageUrl);
        $project->setIsPublished((bool)rand(0,1));
        $project->setSlug($this->slugger->slug($project->getName())->lower());
        $manager->persist($project);
    }
    $manager->flush();
    }
}
