<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;


#[ORM\Entity]
#[ORM\Table(name: '`user`')]
class User implements UserInterface,PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', unique: true)]
    private string $email;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    private string $password;

    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    #[ORM\OneToOne(mappedBy: "user", targetEntity: UserProfile::class, cascade: ["persist", "remove"])]
    private ?UserProfile $userProfile = null;

    #[ORM\OneToOne(mappedBy: "user", targetEntity: OrganizerProfile::class, cascade: ["persist", "remove"])]
    private ?OrganizerProfile $organizerProfile = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $createdAt;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $updatedAt;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $verificationToken = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $resetPasswordToken = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $resetPasswordRequestedAt = null;



    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    // --- Getters & setters for all fields ---

    public function getId(): int { return $this->id; }

    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): void { $this->email = $email; }

    public function getRoles(): array { return $this->roles; }
    public function setRoles(array $roles): void { $this->roles = $roles; }

    public function getPassword(): string { return $this->password; }
    public function setPassword(string $password): void { $this->password = $password; }

    public function isVerified(): bool { return $this->isVerified; }
    public function setIsVerified(bool $isVerified): void { $this->isVerified = $isVerified; }

    public function getUserProfile(): ?UserProfile { return $this->userProfile; }
    public function setUserProfile(?UserProfile $profile): void { $this->userProfile = $profile; }

    public function getOrganizerProfile(): ?OrganizerProfile { return $this->organizerProfile; }
    public function setOrganizerProfile(?OrganizerProfile $profile): void
    {
        $this->organizerProfile = $profile;
    }

public function __toString(): string
{
    return $this->getEmail(); 
}

    public function getCreatedAt(): \DateTimeInterface { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeInterface { return $this->updatedAt; }

    public function setCreatedAt(\DateTimeInterface $createdAt): void { $this->createdAt = $createdAt; }
    public function setUpdatedAt(\DateTimeInterface $updatedAt): void { $this->updatedAt = $updatedAt; }
    

    public function eraseCredentials():void {}
    public function getUserIdentifier(): string { return $this->email; }

    public function getVerificationToken(): ?string
    {
        return $this->verificationToken;
    }

    public function setVerificationToken(?string $verificationToken): self
    {
        $this->verificationToken = $verificationToken;
        return $this;
    }

    public function getResetPasswordToken(): ?string
    {
        return $this->resetPasswordToken;
    }

    public function setResetPasswordToken(?string $token): self
    {
        $this->resetPasswordToken = $token;
        return $this;
    }

    public function getResetPasswordRequestedAt(): ?\DateTimeInterface
    {
        return $this->resetPasswordRequestedAt;
    }

    public function setResetPasswordRequestedAt(?\DateTimeInterface $dateTime): self
    {
        $this->resetPasswordRequestedAt = $dateTime;
        return $this;
    }




    public function getFullName(): string
    {
        return trim($this->firstName . ' ' . $this->lastName);
    }
}
