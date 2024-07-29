<?php

namespace App\Entity;

use App\Repository\TaxeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaxeRepository::class)]
class Taxe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private ?float $rate = null;

    /**
     * @var Collection<int, NoteFrais>
     */
    #[ORM\OneToMany(targetEntity: NoteFrais::class, mappedBy: 'taxe')]
    private Collection $NoteFrais;

    public function __construct()
    {
        $this->NoteFrais = new ArrayCollection();
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

    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function setRate(float $rate): static
    {
        $this->rate = $rate;

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
            $devi->setTaxe($this);
        }

        return $this;
    }

    public function removeDevi(NoteFrais $devi): static
    {
        if ($this->NoteFrais->removeElement($devi)) {
            // set the owning side to null (unless already changed)
            if ($devi->getTaxe() === $this) {
                $devi->setTaxe(null);
            }
        }

        return $this;
    }
}
