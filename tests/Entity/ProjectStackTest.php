<?php

namespace App\Tests\Entity;

use App\Entity\Project;
use App\Entity\BackendLanguage;
use App\Entity\FrontendLanguage;
use App\Entity\Tool;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

class ProjectStackTest extends TestCase
{
    public function testProjectStackBackendLanguage(): void
    {
        $project = new Project();
        $project->setName('Projectback 1');
        $project->setSlug('projectback-1');
        $project->setDescription('Description of projectback 1');
        $project->setCreatedAt(new DateTimeImmutable());
        $project->setUpdatedAt(new \DateTimeImmutable());

        $backendLanguage = new BackendLanguage();
        $backendLanguage->setName('PHPtest');
        $backendLanguage->setSlug('phptest');
        $backendLanguage->setCreatedAt(new DateTimeImmutable());
        $backendLanguage->setUpdatedAt(new \DateTimeImmutable());

        $project->addBackendLanguage($backendLanguage);

        $this->assertEquals('PHPtest', $project->getBackendLanguages()[0]->getName());
        $this->assertEquals('phptest', $project->getBackendLanguages()[0]->getSlug());
        $this->assertInstanceOf(\DateTimeImmutable::class, $project->getBackendLanguages()[0]->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $project->getBackendLanguages()[0]->getUpdatedAt());
    }

    public function testProjectStackFrontendLanguage(): void
    {
        $project = new Project();
        $project->setName('Projectfront 1');
        $project->setSlug('projectfront-1');
        $project->setDescription('Description of projectfront 1');
        $project->setCreatedAt(new DateTimeImmutable());
        $project->setUpdatedAt(new \DateTimeImmutable());

        $frontendLanguage = new FrontendLanguage();
        $frontendLanguage->setName('HTMLtest');
        $frontendLanguage->setSlug('htmltest');
        $frontendLanguage->setCreatedAt(new DateTimeImmutable());
        $frontendLanguage->setUpdatedAt(new \DateTimeImmutable());

        $project->addFrontendLanguage($frontendLanguage);

        $this->assertEquals('HTMLtest', $project->getFrontendLanguages()[0]->getName());
        $this->assertEquals('htmltest', $project->getFrontendLanguages()[0]->getSlug());
        $this->assertInstanceOf(\DateTimeImmutable::class, $project->getFrontendLanguages()[0]->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $project->getFrontendLanguages()[0]->getUpdatedAt());
    }

    public function testProjectStackTools(): void
    {
        $project = new Project();
        $project->setName('Projecttools 1');
        $project->setSlug('projecttools-1');
        $project->setDescription('Description of projecttools 1');
        $project->setCreatedAt(new DateTimeImmutable());
        $project->setUpdatedAt(new \DateTimeImmutable());

        $tool = new Tool();
        $tool->setName('Toolstest');
        $tool->setSlug('toolstest');
        $tool->setCreatedAt(new DateTimeImmutable());
        $tool->setUpdatedAt(new \DateTimeImmutable());

        $project->addTool($tool);

        $this->assertEquals('Toolstest', $project->getTools()[0]->getName());
        $this->assertEquals('toolstest', $project->getTools()[0]->getSlug());
        $this->assertInstanceOf(\DateTimeImmutable::class, $project->getTools()[0]->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $project->getTools()[0]->getUpdatedAt());
    }
}
