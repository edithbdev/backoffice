<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;

trait DeletedTrait
{
    #[ORM\Column(
        type: 'boolean',
        nullable: true,
    )]
    private ?bool $deleted = false;

    public function getDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): self
    {
        $this->deleted = $deleted;

        return $this;
    }
}
