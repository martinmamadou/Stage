<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Assert\NotBlank()]
    #[Assert\Email()]
    private ?string $email = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Assert\NotBlank()]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank()]
    private ?string $lastName = null;

    /**
     * @var Collection<int, Event>
     */
    #[ORM\OneToMany(targetEntity: Event::class, mappedBy: 'user')]
    private Collection $events;

    #[ORM\Column]
    private ?bool $firstlog = null;

    /**
     * @var Collection<int, EmployeeMovement>
     */
    #[ORM\OneToMany(targetEntity: EmployeeMovement::class, mappedBy: 'user')]
    private Collection $employeeMovements;

    #[ORM\Column(length: 255)]
    private ?string $color = null;

    /**
     * @var Collection<int, NoteFrais>
     */
    #[ORM\OneToMany(targetEntity: NoteFrais::class, mappedBy: 'employe')]
    private Collection $NoteFrais;

    public function __construct()
    {
        $this->events = new ArrayCollection();
        $this->employeeMovements = new ArrayCollection();
        $this->NoteFrais = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
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
     *
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
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

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return Collection<int, Event>
     */
    public function getEvents(): Collection
    {
        return $this->events;
    }

    public function addEvent(Event $event): static
    {
        if (!$this->events->contains($event)) {
            $this->events->add($event);
            $event->setUser($this);
        }

        return $this;
    }

    public function removeEvent(Event $event): static
    {
        if ($this->events->removeElement($event)) {
            // set the owning side to null (unless already changed)
            if ($event->getUser() === $this) {
                $event->setUser(null);
            }
        }

        return $this;
    }

    public function isFirstlog(): ?bool
    {
        return $this->firstlog;
    }

    public function setFirstlog(bool $firstlog): static
    {
        $this->firstlog = $firstlog;

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
            $employeeMovement->setUser($this);
        }

        return $this;
    }

    public function removeEmployeeMovement(EmployeeMovement $employeeMovement): static
    {
        if ($this->employeeMovements->removeElement($employeeMovement)) {
            // set the owning side to null (unless already changed)
            if ($employeeMovement->getUser() === $this) {
                $employeeMovement->setUser(null);
            }
        }

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): static
    {
        $this->color = $color;

        return $this;
    }

    /**
     * @return Collection<int, NoteFrais>
     */
    public function getNoteFrais(): Collection
    {
        return $this->NoteFrais;
    }

    public function addDevi(NoteFrais $devi): static
    {
        if (!$this->NoteFrais->contains($devi)) {
            $this->NoteFrais->add($devi);
            $devi->setEmploye($this);
        }

        return $this;
    }

    public function removeDevi(NoteFrais $devi): static
    {
        if ($this->NoteFrais->removeElement($devi)) {
            // set the owning side to null (unless already changed)
            if ($devi->getEmploye() === $this) {
                $devi->setEmploye(null);
            }
        }

        return $this;
    }
}
