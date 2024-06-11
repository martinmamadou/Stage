<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ClientRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[UniqueEntity(fields:'name',message:'ce nom est deja attribué')]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    #[Assert\Length(max:255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Event>
     */
    #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'client')]
    private Collection $event;

    /**
     * @var Collection<int, Site>
     */
    #[ORM\OneToMany(targetEntity: Site::class, mappedBy: 'client', orphanRemoval: true)]
    private Collection $sites;

    /**
     * @var Collection<int, EmployeeMovement>
     */
    #[ORM\OneToMany(targetEntity: EmployeeMovement::class, mappedBy: 'client')]
    private Collection $employeeMovements;

    public function __construct()
    {
        $this->event = new ArrayCollection();
        $this->sites = new ArrayCollection();
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
            $event->setClient($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->event->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getClient() === $this) {
                $event->setClient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Site>
     */
    public function getSites(): Collection
    {
        return $this->sites;
    }

    public function addSite(Site $site): static
    {
        if (!$this->sites->contains($site)) {
            $this->sites->add($site);
            $site->setClient($this);
        }

        return $this;
    }

    public function removeSite(Site $site): static
    {
        if ($this->sites->removeElement($site)) {
            // set the owning side to null (unless already changed)
            if ($site->getClient() === $this) {
                $site->setClient(null);
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
            $employeeMovement->setClient($this);
        }

        return $this;
    }

    public function removeEmployeeMovement(EmployeeMovement $employeeMovement): static
    {
        if ($this->employeeMovements->removeElement($employeeMovement)) {
            // set the owning side to null (unless already changed)
            if ($employeeMovement->getClient() === $this) {
                $employeeMovement->setClient(null);
            }
        }

        return $this;
    }
}
