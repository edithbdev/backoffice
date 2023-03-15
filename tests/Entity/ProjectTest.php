<?php

namespace App\Tests\Entity;

use DateTimeImmutable;
use App\Entity\Project;
use PHPUnit\Framework\TestCase;

class ProjectTest extends TestCase
{
    public function testProject()
    {
        $project = new Project();
        $project->setName('Project 1');
        $project->setSlug('project-1');
        $project->setDescription('Description of project 1');
        $project->setCreatedAt(new DateTimeImmutable());
        $project->setUpdatedAt(new \DateTimeImmutable());

        $this->assertEquals('Project 1', $project->getName());
        $this->assertEquals('project-1', $project->getSlug());
        $this->assertEquals('Description of project 1', $project->getDescription());
        $this->assertInstanceOf(\DateTimeImmutable::class, $project->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $project->getUpdatedAt());
    }

    public function testProjectWithEmptyValues()
    {
        $project = new Project();
        $project->setName('');
        $project->setSlug('');
        $project->setDescription('');

        $this->assertEmpty($project->getName());
        $this->assertEmpty($project->getSlug());
        $this->assertEmpty($project->getDescription());
    }
}
