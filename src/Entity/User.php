<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
        /**
         * @ORM\Id()
         * @ORM\GeneratedValue()
         * @ORM\Column(type="integer")
         */
        private $id;

        /**
         * @ORM\Column(type="string", length=180, unique=true)
         * @Assert\NotBlank()
         * @Assert\Length(min=3, max=20)
         */
        private $username;

        /**
         * @ORM\Column(type="json")
         */
        private $roles = [];

        /**
         * @var string The hashed password
         * @ORM\Column(type="string")
         * @Assert\NotBlank()
         * @Assert\Length(min=6, max=4096)
         */
        private $password;

        /**
         * @ORM\Column(type="string", unique=true, nullable=true)
         */
        private $apiToken;

        /**
        * @ORM\Column(type="string", length=255, nullable=true)
        */
        private $reset_token;

        /**
        * @ORM\Column(type="datetime_immutable")
        */
        private $created_At;

        /**
        * @ORM\Column(type="datetime_immutable", nullable=true)
        */
        private $updated_At;

        public function __construct()
        {
                $this->created_At = new \DateTimeImmutable();
                $this->updated_At = new \DateTimeImmutable();
        }

        public function __toString()
        {
                return $this->username;
        }

        public function getId(): ?int
        {
                return $this->id;
        }

        /**
         * A visual identifier that represents this user.
         *
         * @see UserInterface
         */
        public function getUsername(): string
        {
                return (string) $this->username;
        }

        public function setUsername(string $username): self
        {
                $this->username = $username;

                return $this;
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

        public function setRoles(array $roles): self
        {
                $this->roles = $roles;

                return $this;
        }

        /**
         * @see UserInterface
         */
        public function getPassword(): string
        {
                return (string) $this->password;
        }

        public function setPassword(string $password): self
        {
                $this->password = $password;

                return $this;
        }

        /**
         * @see UserInterface
         */
        public function getSalt()
        {
                // not needed when using the "bcrypt" algorithm in security.yaml
        }

        /**
         * @see UserInterface
         */
        public function eraseCredentials()
        {
                // If you store any temporary, sensitive data on the user, clear it here
                // $this->plainPassword = null;
        }

        public function getApiToken(): ?string
        {
            return $this->apiToken;
        }

        public function setApiToken(?string $apiToken): self
        {
            $this->apiToken = $apiToken;

            return $this;
        }

        public function getResetToken(): ?string
        {
            return $this->reset_token;
        }

        public function setResetToken(?string $reset_token): self
        {
            $this->reset_token = $reset_token;

            return $this;
        }

        public function getCreatedAt(): ?\DateTimeImmutable
        {
            return $this->created_At;
        }

        public function setCreatedAt(\DateTimeImmutable $created_At): self
        {
            $this->created_At = $created_At;

            return $this;
        }

        public function getUpdatedAt(): ?\DateTimeImmutable
        {
            return $this->updated_At;
        }

        public function setUpdatedAt(?\DateTimeImmutable $updated_At): self
        {
            $this->updated_At = $updated_At;

            return $this;
        }
}
