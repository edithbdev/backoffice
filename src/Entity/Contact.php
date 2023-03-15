<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Trait\DeletedTrait;
use App\Entity\Trait\TimeStampTrait;
use App\Repository\ContactRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\NotIdenticalTo;

#[ORM\Entity(repositoryClass: ContactRepository::class)]
#[ORM\HasLifecycleCallbacks()]
class Contact
{
    use DeletedTrait;
    use TimeStampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    #[NotIdenticalTo(propertyPath: 'firstName')]
    #[Assert\Length(
        min: 2,
        max: 20,
        minMessage: 'Le nom doit faire au moins {{ limit }} caractères',
        maxMessage: 'Le nom doit faire au plus {{ limit }} caractères'
    )]
    private ?string $lastname = null;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\Length(
        min: 2,
        max: 20,
        minMessage: 'Le prénom doit faire au moins {{ limit }} caractères',
        maxMessage: 'Le prénom doit faire au plus {{ limit }} caractères'
    )]
    private ?string $firstname = null;

    #[ORM\Column(type: 'string', length: 180)]
    #[Assert\NotBlank(message: 'Merci de renseigner un email')]
    #[Assert\Email(
        message: 'The email "{{ value }}" is not a valid email.'
    )]
    private ?string $email = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Assert\Length(
        min: 10,
        max: 255,
        minMessage: 'Le sujet doit faire au moins {{ limit }} caractères',
        maxMessage: 'Le sujet doit faire au plus {{ limit }} caractères'
    )]
    private ?string $subject = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Merci de renseigner un message')]
    private ?string $message = null;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private bool $isRead = false;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private bool $isAnswered = false;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $comment = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(?string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getIsRead(): ?bool
    {
        return $this->isRead;
    }

    public function setIsRead(bool $isRead): self
    {
        $this->isRead = $isRead;

        return $this;
    }

    public function getIsAnswered(): ?bool
    {
        return $this->isAnswered;
    }

    public function setIsAnswered(bool $isAnswered): self
    {
        $this->isAnswered = $isAnswered;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): self
    {
        $this->comment = $comment;

        return $this;
    }
}
