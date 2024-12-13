<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "datetime")]
    private ?\DateTimeInterface $date = null;

    // Le champ timeSlot sera une chaîne pour stocker la plage horaire sous forme de string
    #[ORM\Column(type: "string", length: 255)]
    private ?string $timeSlot = null;

    #[ORM\Column(length: 255)]
    private ?string $eventName = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    private ?User $Relations = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $now = new \DateTime();
        if ($date < $now->modify('+24 hours')) {
            throw new \InvalidArgumentException("Les réservations doivent se faire au moins 24 heures à l'avance.");
        }

        $this->date = $date;

        return $this;
    }

    public function getTimeSlot(): ?string
    {
        return $this->timeSlot;
    }

    // La plage horaire est maintenant une chaîne de caractères
    public function setTimeSlot(string $timeSlot): static
    {
        // Validation de la plage horaire pour s'assurer qu'elle est bien au format "HH:MM-HH:MM"
        if (!preg_match('/^\d{2}:\d{2}-\d{2}:\d{2}$/', $timeSlot)) {
            throw new \InvalidArgumentException("Le format de la plage horaire est invalide. Utilisez le format HH:MM-HH:MM.");
        }

        $this->timeSlot = $timeSlot;

        return $this;
    }

    public function getEventName(): ?string
    {
        return $this->eventName;
    }

    public function setEventName(string $eventName): static
    {
        $this->eventName = $eventName;

        return $this;
    }

    public function getRelations(): ?User
    {
        return $this->Relations;
    }

    public function setRelations(?User $Relations): static
    {
        $this->Relations = $Relations;

        return $this;
    }
}