<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: EventRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank]
    private string $title;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: "datetime")]
    #[Assert\NotBlank]
    private \DateTimeInterface $startDate;

    #[ORM\Column(type: "datetime")]
    #[Assert\NotBlank]
    private \DateTimeInterface $endDate;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $location = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $banner = null;

    #[ORM\Column(type: "string", length: 20)]
    private string $status = 'draft'; 

    #[ORM\Column(type: "boolean")]
    private bool $isDeleted = false;

    #[ORM\Column(type: "datetime_immutable")]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: "datetime_immutable", nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $createdBy;

    #[ORM\Column(type: "integer")]
    private int $totalBookings = 0;

    #[ORM\OneToMany(mappedBy: "event", targetEntity: TicketType::class, cascade: ["persist", "remove"])]
    private Collection $ticketTypes;

    #[ORM\OneToMany(mappedBy: "event", targetEntity: Booking::class)]
    private Collection $bookings;

    #[ORM\PrePersist]
    public function onCreate(): void
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function onUpdate(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function __construct()
    {
        $this->ticketTypes = new ArrayCollection();
        $this->bookings = new ArrayCollection();
    }


    public function getTicketTypes(): Collection
    {
        return $this->ticketTypes;
    }

    public function addTicketType(TicketType $ticketType): void
    {
        if (!$this->ticketTypes->contains($ticketType)) {
            $this->ticketTypes->add($ticketType);
            $ticketType->setEvent($this);
        }
    }

    public function removeTicketType(TicketType $ticketType): void
    {
        if ($this->ticketTypes->removeElement($ticketType)) {
            if ($ticketType->getEvent() === $this) {
                $ticketType->setEvent(null);
            }
        }
    }




    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }
        public function __toString(): string
{
    return $this->getTitle(); // Or getName(), depending on your property
}


    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getStartDate(): \DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): \DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): void
    {
        $this->endDate = $endDate;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): void
    {
        $this->location = $location;
    }

    public function getBanner(): ?string
    {
        return $this->banner;
    }

    public function setBanner(?string $banner): void
    {
        $this->banner = $banner;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getIsDeleted(): bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): void
    {
        $this->isDeleted = $isDeleted;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(User $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function getTotalBookings(): int
    {
        return $this->totalBookings;
    }

    public function setTotalBookings(int $totalBookings): void
    {
        $this->totalBookings = $totalBookings;
    }

    public function getOrganizer(): User
    {
        return $this->createdBy; // Assuming the organizer is the user who created the event
    }

    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function hasBookingByUser(User $user): bool
    {
        foreach ($this->bookings as $booking) {
            if ($booking->getUser() === $user) {
                return true;
            }
        }

        return false;
    }
}
