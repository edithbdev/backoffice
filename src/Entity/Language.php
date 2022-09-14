<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\LanguageRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

#[ApiResource(
    collectionOperations: [
        'get',
        'post'
    ],
    itemOperations: [
        'get',
        'put',
        'patch',
        'delete'
    ],

    attributes: [
        'pagination_enabled' => false,
    ],
)]

#[ApiFilter(SearchFilter::class, properties: [
    'name'=> SearchFilter::STRATEGY_PARTIAL,
    'projects'=> SearchFilter::STRATEGY_PARTIAL,
]
)]

#[ORM\Entity(repositoryClass: LanguageRepository::class)]
class Language
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['collection','item'])]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    #[Groups(['collection','item'])]
    #[NotBlank]
    private ?string $name = null;


    #[ORM\ManyToMany(targetEntity: Project::class, mappedBy: 'Languages')]
    #[ORM\JoinTable(name: 'projects_languages')]
    // #[Groups(['collection'])]
    private Collection $projects;

    public function __construct()
    {
        $this->projects = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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
            $project->addLanguage($this);
        }

        return $this;
    }

    public function removeProject(Project $project): self
    {
        if ($this->projects->removeElement($project)) {
            $project->removeLanguage($this);
        }

        return $this;
    }
}
