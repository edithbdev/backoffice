<?php

namespace App\Entity\Trait;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

trait TimeStampTrait
{
    #[ORM\Column(
        type: 'datetime_immutable',
        options: ['default' => 'CURRENT_TIMESTAMP']
    )]
    #[Groups(['project_read', 'project_list'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(
        nullable: true,
        type: 'datetime_immutable',
        options: [
            'default' => 'CURRENT_TIMESTAMP'
        ]
    )]
    #[Groups(['project_read', 'project_list'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function preUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
