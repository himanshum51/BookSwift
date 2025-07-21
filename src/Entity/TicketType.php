<?php

namespace App\Entity;

use App\Repository\TicketTypeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Event;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ORM\Entity(repositoryClass: TicketTypeRepository::class)]
class TicketType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Event::class, inversedBy: 'ticketTypes')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Event $event;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $description = null;

    #[ORM\Column(type: "integer")]
    #[Assert\PositiveOrZero]
    private int $price;

    #[ORM\Column(type: "integer")]
    #[Assert\PositiveOrZero]
    private int $quantity;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $availableFrom;

    #[ORM\Column(type: "datetime")]
    private \DateTimeInterface $availableTo;

    #[ORM\OneToMany(mappedBy: "ticketType", targetEntity: Booking::class, cascade: ["persist", "remove"])]
    private Collection $bookings;

    public function __construct()
    {
        $this->bookings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEvent(): Event
    {
        return $this->event;
    }

    public function setEvent(Event $event): void
    {
        $this->event = $event;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): void
    {
        $this->price = $price;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    public function getAvailableFrom(): \DateTimeInterface
    {
        return $this->availableFrom;
    }

    public function setAvailableFrom(\DateTimeInterface $availableFrom): void
    {
        $this->availableFrom = $availableFrom;
    }

    public function getAvailableTo(): \DateTimeInterface
    {
        return $this->availableTo;
    }

    public function setAvailableTo(\DateTimeInterface $availableTo): void
    {
        $this->availableTo = $availableTo;
    }

    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): void
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings->add($booking);
            $booking->setTicketType($this);
        }
    }

    public function removeBooking(Booking $booking): void
    {
        if ($this->bookings->removeElement($booking)) {
            if ($booking->getTicketType() === $this) {
                $booking->setTicketType(null);
            }
        }
    }

    public function __toString(): string
{
    return $this->getName(); // Or type or label
}

}
