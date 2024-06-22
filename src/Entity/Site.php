<?php

namespace App\Entity;

use App\Repository\SiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SiteRepository::class)]
class Site
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToOne(inversedBy: 'sites')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    /**
     * @var Collection<int, Event>
     */
    #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'site')]
    private Collection $event;

    /**
     * @var Collection<int, EmployeeMovement>
     */
    #[ORM\OneToMany(targetEntity: EmployeeMovement::class, mappedBy: 'site')]
    private Collection $employeeMovements;

    public function __construct()
    {
        $this->event = new ArrayCollection();
        $this->employeeMovements = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
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

    /**
     * @return Collection<int, Event>
     */
    public function getEvent(): Collection
    {
        return $this->event;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->event->contains($event)) {
            $this->event->add($event);
            $event->setSite($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->event->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getSite() === $this) {
                $event->setSite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EmployeeMovement>
     */
    public function getEmployeeMovements(): Collection
    {
        return $this->employeeMovements;
    }

    public function addEmployeeMovement(EmployeeMovement $employeeMovement): static
    {
        if (!$this->employeeMovements->contains($employeeMovement)) {
            $this->employeeMovements->add($employeeMovement);
            $employeeMovement->setSite($this);
        }

        return $this;
    }

    public function removeEmployeeMovement(EmployeeMovement $employeeMovement): static
    {
        if ($this->employeeMovements->removeElement($employeeMovement)) {
            // set the owning side to null (unless already changed)
            if ($employeeMovement->getSite() === $this) {
                $employeeMovement->setSite(null);
            }
        }

        return $this;
    }
}
