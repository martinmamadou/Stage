<?php

namespace App\Entity;

use App\Repository\EmployeeMovementRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EmployeeMovementRepository::class)]
class EmployeeMovement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'employeeMovements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\ManyToOne(inversedBy: 'employeeMovements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    private ?string $moveDescription = null;

    #[ORM\ManyToOne(inversedBy: 'Employeemove')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Event $event = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\ManyToOne(inversedBy: 'employeeMovements')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Site $site = null;

    #[ORM\Column]
    private ?int $groupe = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $end = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getMoveDescription(): ?string
    {
        return $this->moveDescription;
    }

    public function setMoveDescription(string $moveDescription): static
    {
        $this->moveDescription = $moveDescription;

        return $this;
    }

    public function getEvent(): ?Event
    {
        return $this->event;
    }

    public function setEvent(?Event $event): static
    {
        $this->event = $event;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): static
    {
        $this->site = $site;

        return $this;
    }

    public function getGroupe(): ?int
    {
        return $this->groupe;
    }

    public function setGroupe(int $groupe): static
    {
        $this->groupe = $groupe;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end): static
    {
        $this->end = $end;

        return $this;
    }
}
