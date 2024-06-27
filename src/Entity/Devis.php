<?php
namespace App\Entity;

use App\Repository\DevisRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DevisRepository::class)]
class Devis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\ManyToOne(inversedBy: 'devis')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\ManyToOne(inversedBy: 'devis')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $employe = null;

    #[ORM\Column(length: 255)]
    private ?string $categorie = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $prixHt = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $quantite = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $km = null;

    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $prixKm = null;

    #[ORM\Column(type: 'float')]
    private ?float $totalTTC = null;

    #[ORM\ManyToOne(inversedBy: 'devis')]
    private ?Taxe $taxe = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getEmploye(): ?User
    {
        return $this->employe;
    }

    public function setEmploye(?User $employe): static
    {
        $this->employe = $employe;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getPrixHt(): ?float
    {
        return $this->prixHt;
    }

    public function setPrixHt(float $prixHt): static
    {
        $this->prixHt = $prixHt;
        $this->calculateTotalTTC();
        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): static
    {
        $this->quantite = $quantite;
        $this->calculateTotalTTC();
        return $this;
    }

    public function getKm(): ?float
    {
        return $this->km;
    }

    public function setKm(?float $km): static
    {
        $this->km = $km;
        $this->calculateTotalTTC();
        return $this;
    }

    public function getPrixKm(): ?float
    {
        return $this->prixKm;
    }

    public function setPrixKm(?float $prixKm): static
    {
        $this->prixKm = $prixKm;

        return $this;
    }

    public function getTotalTTC(): ?float
    {
        return $this->totalTTC;
    }

    public function setTotalTTC(float $totalTTC): static
    {
        $this->totalTTC = $totalTTC;

        return $this;
    }

    public function getTaxe(): ?Taxe
    {
        return $this->taxe;
    }

    public function setTaxe(?Taxe $taxe): static
    {
        $this->taxe = $taxe;
        $this->calculateTotalTTC();
        return $this;
    }

    private function calculateTotalTTC(): void
    {
        if ($this->km === null) {
            if ($this->prixHt !== null && $this->quantite !== null) {
                $totalHt = $this->prixHt * $this->quantite;
                if ($this->taxe !== null) {
                    $totalTTC = $totalHt * (1 + $this->taxe->getRate());
                } else {
                    $totalTTC = $totalHt;
                }
                $this->totalTTC = round($totalTTC, 2);
            }
        } else {
            $this->totalTTC = 15;
        }
    }
}

