<?php

namespace App\Entity;

use App\Entity\Project;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Trait\DeletedTrait;
use App\Repository\ImagesRepository;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ImagesRepository::class)]
class Images
{
    use DeletedTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['project_read'])]
    private ?int $id = null;


    #[ORM\Column(length: 255, type: 'string')]
    #[Groups(['project_read'])]
    private string $name;


    #[ORM\Column(length: 255)]
    #[Groups(['project_read'])]
    private ?string $path = null;


    #[ORM\ManyToOne(inversedBy: 'images')]
    private ?Project $project = null;

    public function __toString()
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return (string) $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPath(): ?string
    {
        return '/uploads/images/' . $this->name;
    }

    public function setPath(string $path): self
    {
        $this->path = $path;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }
}
