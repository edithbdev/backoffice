<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\Enum\Status;
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
        $faker = Faker\Factory::create('fr_FR');

        for ($proj = 1; $proj <= 15; $proj++) {
            $project = new Project();
            $project->setName($faker->text(15));
            $project->setDescription($faker->text());
            $project->setProjectLink($faker->imageUrl);
            $project->setGithubLink($faker->url);
            $project->setyear($faker->year);
            $project->setStatus($this->randomElement([Status::Draft, Status::Published]));
            $project->setSlug($this->slugger->slug($project->getName())->lower());
            $manager->persist($project);
        }
        $manager->flush();
    }

    /**
     * @param array<mixed> $array
     * @return mixed
     */
    public function randomElement(array $array): mixed
    {
        return $array[array_rand($array)];
    }
}
