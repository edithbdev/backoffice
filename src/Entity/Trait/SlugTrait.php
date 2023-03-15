<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

trait SlugTrait
{
    #[ORM\Column(
        nullable: true,
        type: 'string',
    )]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: 'Le slug doit faire au moins {{ limit }} caractères',
        maxMessage: 'Le slug doit faire au plus {{ limit }} caractères'
    )]
    #[Groups(['project_read', 'project_list'])]
    private ?string $slug = null;

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }
}
