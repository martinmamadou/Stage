<?php
namespace App\Entity;

use App\Entity\Traits\DateTimeTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\NoteFraisRepository;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;

#[ORM\Entity(repositoryClass: NoteFraisRepository::class)]
#[HasLifecycleCallbacks]
class NoteFrais
{
    use DateTimeTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\ManyToOne(inversedBy: 'NoteFrais')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\ManyToOne(inversedBy: 'NoteFrais')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $employe = null;

    #[ORM\Column(length: 255, nullable:true)]
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

    #[ORM\ManyToOne(inversedBy: 'NoteFrais')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Taxe $taxe = null;

    #[ORM\Column(nullable: true)]
    private ?bool $Carte_client = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $creation = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Forfait $forfait = null;

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function updateTotalTTC(): void
    {
        $this->calculateTotalTTC();
    }

    private function calculateTotalTTC(): void
    {
        if ($this->km === null) {
            if ($this->prixHt !== null && $this->quantite !== null) {
                $totalHt = $this->prixHt * $this->quantite;
                $totalTaxe = $this->getTotalTaxe();
                $this->totalTTC = round($totalHt + $totalTaxe, 2);
            } else if ($this->categorie === 'forfait' && $this->prixHt !== null) {
                $this->totalTTC = $this->prixHt;
            } else {
                $this->totalTTC = 0;  // Default value to avoid null
            }
        } else {
            $this->totalTTC = $this->km * (1+0.057);
        }
    }

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

    public function getTotalTaxe(): float
    {
        if ($this->prixHt !== null && $this->quantite !== null && $this->taxe !== null) {
            return round($this->prixHt * $this->quantite * $this->taxe->getRate(), 2);
        }
        return 0.0;
    }

    public function isCarteClient(): ?bool
    {
        return $this->Carte_client;
    }

    public function setCarteClient(bool $Carte_client): static
    {
        $this->Carte_client = $Carte_client;
        return $this;
    }

    public function getCreation(): ?\DateTimeInterface
    {
        return $this->creation;
    }

    public function setCreation(?\DateTimeInterface $creation): static
    {
        $this->creation = $creation;
        return $this;
    }

    public function getForfait(): ?Forfait
    {
        return $this->forfait;
    }

    public function setForfait(?Forfait $forfait): static
    {
        $this->forfait = $forfait;
        return $this;
    }
}
