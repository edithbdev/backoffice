<?php

namespace App\Entity;

use App\Entity\Trait\SlugTrait;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Trait\CreatedAtTrait;
use App\Entity\Trait\UpdatedAtTrait;
use App\Repository\ProjectRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\HttpFoundation\Request;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Controller\Api\RandomProjectController;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;

#[ApiResource(
    /*collectionOperations permet de définir les opérations
    disponibles sur la collection */
    collectionOperations: [
        'get' => [
            //normalisation on passe de l'objet à un tableau et ensuite au json
            'normalization_context' => ['groups' => ['collection']],
        ],
        'post',
        // Custom route name
        'random' => [
            'method' => Request::METHOD_GET,
            'path' => '/projects/random',
            'output' => Project::class,
            'controller' => RandomProjectController::class,
            'paging_enabled' => false,
            'normalization_context' => ['groups' => ['item']],
            // 'openapi_context' => [
            //     'summary' => 'Get a random project',
            //     'description' => 'Get a random project',
            // ],
        ],
    ],
    /* itemOperations permet de définir les opérations
    disponibles sur un item */
    itemOperations: [
        'get' => [
            'normalization_context' => ['groups' => ['item']],
        ],
        'put',
        'patch',
        'delete'
    ],
    /* //denormalisation on passe du json au tableau et ensuite à l'objet */
    denormalizationContext: ['groups' => ['write']],
    /* attributes permet de définir des attributs, par exemple
    pour activer ou désactiver la pagination */
    attributes: [
        'pagination_enabled' => false
    ],
)]
/* filtre de recherche sur les noms..., + recherche partielle */
#[ApiFilter(SearchFilter::class, properties: [
    'name'=> SearchFilter::STRATEGY_PARTIAL,
    'description'=> SearchFilter::STRATEGY_PARTIAL,
    'languages'=> SearchFilter::STRATEGY_PARTIAL,
    ]
)]
#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project
{
    //les traits sont des méthodes qui sont appelées automatiquement
    use CreatedAtTrait;
    use UpdatedAtTrait;
    use SlugTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    //Groups permet de sélectionner les attributs à afficher
    #[Groups(['collection', 'item'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['collection','item', 'write'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['collection','item', 'write'])]
    private ?string $description = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['collection','item', 'write'])]
    private ?string $image = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['collection','item', 'write'])]
    private ?string $link = null;

    #[ORM\Column(type: 'boolean', options:[ 'default' => '0'])]
    #[Groups(['collection','item', 'write'])]
    private ?bool $isPublished = null;

    #[ORM\ManyToMany(targetEntity: Language::class, inversedBy: 'projects')]
    // JoinTable permet de définir le nom de la table intermédiaire
    #[ORM\JoinTable(name: 'projects_languages')]
    // ApiSubresource permet d'ajouter une sous-ressource
    #[ApiSubresource]
    #[Groups(['collection','item', 'write'])]
    private Collection $Languages;

    public function __construct()
    {
        $this->Languages = new ArrayCollection();
        $this->created_at = new \DateTimeImmutable();
        $this->updated_at = new \DateTimeImmutable();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function isIsPublished(): ?bool
    {
        return $this->isPublished;
    }

    public function setIsPublished(?bool $isPublished): self
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * @return Collection<int, Language>
     */
    public function getLanguages(): Collection
    {
        return $this->Languages;
    }

    public function addLanguage(Language $language): self
    {
        if (!$this->Languages->contains($language)) {
            $this->Languages->add($language);
        }

        return $this;
    }

    public function removeLanguage(Language $language): self
    {
        $this->Languages->removeElement($language);

        return $this;
    }
}
