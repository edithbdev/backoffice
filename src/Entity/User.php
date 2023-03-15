<?php

namespace App\Entity;

use App\Entity\Trait\DeletedTrait;
use Doctrine\DBAL\Types\Types;
use ORM\HasLifecycleCallbacks;
use App\Entity\Trait\SlugTrait;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use App\Entity\Trait\TimeStampTrait;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\NotIdenticalTo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(
    fields: ['email'],
    message: 'There is already an account with this email'
)]
#[ORM\HasLifecycleCallbacks()]

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use DeletedTrait;
    use TimeStampTrait;
    use SlugTrait;

    // indique que l'attribut est la clé primaire
    #[ORM\Id]
    // auto-increment
    #[ORM\GeneratedValue]
    // indique le type de la colonne est integer
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\NotBlank(message: 'Merci de renseigner un email')]
    #[Assert\Email(
        mode: "html5",
        message: 'The email "{{ value }}" is not a valid email.'
    )]
    private ?string $email;

    /**
     * @var array<string> Le rôle de l'utilisateur
     */
    #[ORM\Column(type: 'json')]
    #[Assert\All([
        new Assert\NotBlank(),
        new Assert\Choice(choices: ['ROLE_USER', 'ROLE_ADMIN'])
    ])]
    private array $roles = [];

    /**
     * @var string Password de l'utilisateur
     */
    #[ORM\Column]
    #[Assert\Length(min: 6, max: 80)]
    #[Assert\NotBlank(message: 'Merci de renseigner un mot de passe')]
    private ?string $password;

    #[ORM\Column(length: 100)]
    #[NotIdenticalTo(propertyPath: 'firstName')]
    #[Assert\Length(min: 2, max: 20)]
    #[Assert\NotBlank(message: 'Merci de renseigner un nom')]
    private ?string $lastname;

    #[ORM\Column(length: 100)]
    #[Assert\Length(min: 2, max: 20)]
    #[Assert\NotBlank(message: 'Merci de renseigner un prénom')]
    private ?string $firstname;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private ?bool $isVerified = false;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $resetToken = null;

    #[ORM\Column(nullable: true)]
    private ?int $countSession = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $LastLogin = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param array<string> $roles
     * @return $this
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     * @param string $password
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
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

    public function getIsVerified(): ?bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getResetToken(): ?string
    {
        return $this->resetToken;
    }

    public function setResetToken(?string $resetToken): self
    {
        $this->resetToken = $resetToken;

        return $this;
    }

    public function getCountSession(): ?int
    {
        return $this->countSession;
    }

    public function setCountSession(?int $countSession): self
    {
        $this->countSession = $countSession;

        return $this;
    }

    public function getLastLogin(): ?\DateTimeInterface
    {
        return $this->LastLogin;
    }

    public function setLastLogin(?\DateTimeInterface $LastLogin): self
    {
        $this->LastLogin = $LastLogin;

        return $this;
    }
}
