<?php

namespace App\DataFixtures;

use App\Entity\Tool;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class ToolFixtures extends Fixture
{
    public function __construct(private SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $this->createTool($manager, 'Server');
        $this->createTool($manager, 'Client');
        $this->createTool($manager, 'Mobile');
        $this->createTool($manager, 'Database');
        $this->createTool($manager, 'CI/CD');
        $this->createTool($manager, 'Docker');
        $this->createTool($manager, 'Cloud');
        $this->createTool($manager, 'CMS');
        $this->createTool($manager, 'API');
        $this->createTool($manager, 'Design');
        $this->createTool($manager, 'SEO');
        $this->createTool($manager, 'Testing');
        $this->createTool($manager, 'Security');
        $this->createTool($manager, 'Other');

        $manager->flush();
    }

    public function createTool(ObjectManager $manager, string $name): Tool
    {
        $tool = new Tool();
        $tool->setName($name);
        $tool->setDescription('Description of ' . $name);
        $tool->setSlug($this->slugger->slug($name)->lower());
        $manager->persist($tool);
        return $tool;
    }
}
