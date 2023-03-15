<?php

namespace App\Entity;

use App\Entity\Enum\Status;
use App\Entity\Tool;
use App\Entity\Trait\SlugTrait;
use App\Entity\BackendLanguage;
use App\Entity\FrontendLanguage;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Trait\DeletedTrait;
use App\Entity\Trait\TimeStampTrait;
use App\Repository\ProjectRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
#[Vich\Uploadable]
//Lifecycle callback Doctrine events are triggered by the EntityManager when an entity is persisted, updated or removed.
#[ORM\HasLifecycleCallbacks()]
#[ORM\Table(name: 'projects')]
#[ORM\Index(columns: ['name', 'description'], flags: ['fulltext'])]

class Project
{
    use DeletedTrait;
    use TimeStampTrait;
    use SlugTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['project_read', 'project_list'])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', enumType: Status::class, options: ['default' => Status::Draft])]
    #[Assert\NotBlank(message: 'Merci de choisir un statut')]
    #[Groups(['project_read', 'project_list'])]
    private Status $status;

    #[ORM\Column(type: 'string')]
    #[Assert\Length(
        min: 2,
        max: 25,
        minMessage: 'Le nom doit faire au moins {{ limit }} caractères',
        maxMessage: 'Le nom doit faire au plus {{ limit }} caractères'
    )]
    #[Assert\NotBlank(message: 'Merci de renseigner un nom')]
    #[Groups(['project_read', 'project_list'])]
    private string $name;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank(message: 'Merci de renseigner une description')]
    #[Groups(['project_read', 'project_list'])]
    private ?string $description = null;

    #[Vich\UploadableField(mapping: 'project_images', fileNameProperty: 'imageName')]
    #[Groups(['project_read', 'project_list'])]
    private ?File $imageFile = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[Groups(['project_read', 'project_list'])]
    private ?string $imageName = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: 'Le lien doit faire au moins {{ limit }} caractères',
        maxMessage: 'Le lien doit faire au plus {{ limit }} caractères'
    )]
    #[Assert\Url(message: 'Le lien doit être une URL valide')]
    #[Groups(['project_read'])]
    private ?string $projectLink = null;

    #[ORM\Column(nullable: true)]
    #[Assert\Length(
        min: 5,
        max: 255,
        minMessage: 'Le lien doit faire au moins {{ limit }} caractères',
        maxMessage: 'Le lien doit faire au plus {{ limit }} caractères'
    )]
    #[Assert\Url(message: 'Le lien doit être une URL valide')]
    #[Groups(['project_read'])]
    private ?string $githubLink = null;

    #[ORM\Column(type: 'string', nullable: true)]
    #[Assert\Length(
        min: 4,
        max: 4,
        minMessage: 'L\'année doit faire {{ limit }} caractères',
        maxMessage: 'L\'année doit faire {{ limit }} caractères'
    )]
    #[Groups(['project_read', 'project_list'])]
    private ?string $year = null;

    #[ORM\Column(type: 'date', nullable: true)]
    #[Groups(['project_read', 'project_list'])]
    private ?\DateTimeInterface $lastUpdate = null;

    /**
     * @var Collection<int, FrontendLanguage>
     */
    #[ORM\ManyToMany(targetEntity: FrontendLanguage::class, inversedBy: 'projects')]
    #[Groups(['project_read', 'project_list'])]
    private Collection $frontendLanguages;

    /**
     * @var Collection<int, BackendLanguage>
     */
    #[ORM\ManyToMany(targetEntity: BackendLanguage::class, inversedBy: 'projects')]
    #[Groups(['project_read', 'project_list'])]
    private Collection $backendLanguages;

    /**
     * @var Collection<int, Tool>
     */
    #[ORM\ManyToMany(targetEntity: Tool::class, inversedBy: 'projects')]
    #[Groups(['project_read', 'project_list'])]
    private Collection $tools;

    /**
     * @var Collection<int, Images>
     */
    #[ORM\OneToMany(mappedBy: 'project', targetEntity: Images::class, cascade: ['persist', 'remove'], orphanRemoval: true)]//phpcs:ignore
    #[Groups(['project_read'])]
    private Collection $images;

    public function __construct()
    {
        $this->frontendLanguages = new ArrayCollection();
        $this->backendLanguages = new ArrayCollection();
        $this->tools = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
        $this->lastUpdate = new \DateTimeImmutable();
        $this->images = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->name;
    }
    public function getId(): ?int
    {
        return $this->id;
    }
    public function getStatus(): Status
    {
        return $this->status;
    }
    public function setStatus(Status $status): self
    {
        $this->status = $status;
        return $this;
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
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;
        if (null !== $imageFile) {
            $this->updatedAt = new \DateTimeImmutable();
        }
    }
    public function getImageName(): ?string
    {
        return $this->imageName;
    }
    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }
    public function getProjectLink(): ?string
    {
        return $this->projectLink;
    }
    public function setProjectLink(?string $projectLink): self
    {
        $this->projectLink = $projectLink;
        return $this;
    }

    public function getGithubLink(): ?string
    {
        return $this->githubLink;
    }

    public function setGithubLink(?string $githubLink): self
    {
        $this->githubLink = $githubLink;
        return $this;
    }

    public function getYear(): ?string
    {
        return $this->year;
    }

    public function setYear(?string $year): self
    {
        $this->year = $year;
        return $this;
    }

    public function getLastUpdate(): ?\DateTimeInterface
    {
        return $this->lastUpdate;
    }

    public function setLastUpdate(?\DateTimeInterface $lastUpdate): self
    {
        $this->lastUpdate = $lastUpdate;
        return $this;
    }

    /**
     * @return Collection<int, FrontendLanguage>
     */
    public function getFrontendLanguages(): Collection
    {
        return $this->frontendLanguages;
    }

    public function addFrontendLanguage(FrontendLanguage $frontendLanguage): self
    {
        if (!$this->frontendLanguages->contains($frontendLanguage)) {
            $this->frontendLanguages->add($frontendLanguage);
        }
        return $this;
    }
    public function removeFrontendLanguage(FrontendLanguage $frontendLanguage): self
    {
        $this->frontendLanguages->removeElement($frontendLanguage);
        return $this;
    }

    /**
     * @return Collection<int, BackendLanguage>
     */
    public function getBackendLanguages(): Collection
    {
        return $this->backendLanguages;
    }

    public function addBackendLanguage(BackendLanguage $backendLanguage): self
    {
        if (!$this->backendLanguages->contains($backendLanguage)) {
            $this->backendLanguages->add($backendLanguage);
        }
        return $this;
    }

    public function removeBackendLanguage(BackendLanguage $backendLanguage): self
    {
        $this->backendLanguages->removeElement($backendLanguage);
        return $this;
    }

    /**
     * @return Collection<int, Tool>
     */
    public function getTools(): Collection
    {
        return $this->tools;
    }

    public function addTool(Tool $tool): self
    {
        if (!$this->tools->contains($tool)) {
            $this->tools->add($tool);
        }

        return $this;
    }

    public function removeTool(Tool $tool): self
    {
        $this->tools->removeElement($tool);

        return $this;
    }

    /**
     * @return Collection<int, Images>
     */
    public function getImages(): Collection
    {
        return $this->images;
    }

    public function addImage(Images $image): self
    {
        if (!$this->images->contains($image)) {
            $this->images->add($image);
            $image->setProject($this);
        }

        return $this;
    }

    public function removeImage(Images $image): self
    {
        if ($this->images->removeElement($image)) {
            // set the owning side to null (unless already changed)
            if ($image->getProject() === $this) {
                $image->setProject(null);
            }
        }

        return $this;
    }
}
