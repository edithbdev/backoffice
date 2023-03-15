<?php

namespace App\Entity;

use DateTimeImmutable;
use App\Entity\Project;
use App\Entity\Trait\SlugTrait;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Trait\DeletedTrait;
use App\Repository\ToolRepository;
use App\Entity\Trait\TimeStampTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ToolRepository::class)]
#[UniqueEntity(
    fields: ['name', 'description'],
    message: 'There is already a tool with this name and description'
)]

#[ORM\Index(columns: ['name'], flags: ['fulltext'])]
class Tool
{
    use DeletedTrait;
    use SlugTrait;
    use TimeStampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['project_read', 'project_list'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Le nom doit faire au moins {{ limit }} caractères',
        maxMessage: 'Le nom doit faire au plus {{ limit }} caractères'
    )]
    #[Assert\NotBlank(message: 'Merci de renseigner un nom')]
    #[Groups(['project_read', 'project_list'])]
    private string $name;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'Merci de renseigner une description')]
    #[Groups(['project_read'])]
    private ?string $description = null;

    /**
     * @var Collection<int, Project>
     */
    #[ORM\ManyToMany(targetEntity: Project::class, mappedBy: 'tools')]
    #[ORM\JoinTable(name: 'projects_tools')]
    private Collection $projects;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection<int, Project>
     */
    public function getProjects(): Collection
    {
        return $this->projects;
    }

    public function addProject(Project $project): self
    {
        if (!$this->projects->contains($project)) {
            $this->projects->add($project);
            $project->addTool($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->contains($project)) {
            if ($project->getTools()->contains($this)) {
                $project->removeTool($this);
            }
        }
        return $this;
    }
}
