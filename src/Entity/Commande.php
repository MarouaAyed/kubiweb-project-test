<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommandeRepository")
 */
class Commande
{
    public function __construct()
    {
        $this->lignes = new ArrayCollection();
        $this->status = 'En cours';
    }

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="string", length=20, unique=true)
     */
    private $numCmd;

    /**
     * @ORM\Column(type="datetime")
     */

    private $dateCommande;

    /**
     * @ORM\Column(type="float", nullable=false)
     */
    private $total = 0;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Client", inversedBy="commandes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LigneCommande", mappedBy="commande", cascade={"persist", "remove"})
     */
    private $lignes;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumCmd(): ?string
    {
        return $this->numCmd;
    }

    public function setNumCmd(string $numCmd): self
    {
        $this->numCmd = $numCmd;
        return $this;
    }


    public function getDateCommande(): ?\DateTimeInterface
    {
        return $this->dateCommande;
    }

    public function setDateCommande(\DateTimeInterface $dateCommande): self
    {
        $this->dateCommande = $dateCommande;
        return $this;
    }

    public function getTotal(): ?float
    {
        return $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;
        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;
        return $this;
    }

    /**
     * Compute the total from associated lines.
     */
    public function getTotalCommande(): float
    {
        $total = 0;
        foreach ($this->lignes as $ligne) {
            $total += $ligne->getTotal();
        }
        return $total;
    }

    /**
     * @return Collection<int, LigneCommande>
     */
    public function getLignes(): Collection
    {
        return $this->lignes;
    }

    /**
     * @param Collection<int, LigneCommande> $lignes
     */
    public function setLignes(Collection $lignes): self
    {
        $this->lignes = $lignes;

        return $this;
    }

    public function addLigne(LigneCommande $ligne): self
    {
        if (!$this->lignes->contains($ligne)) {
            $this->lignes[] = $ligne;
            $ligne->setCommande($this);

            $this->total += $ligne->getQuantite() * $ligne->getProduit()->getPrixTtc();
        }

        $this->setTotal($this->total);

        return $this;
    }


    public function removeLigne(LigneCommande $ligne): self
    {
        if ($this->lignes->removeElement($ligne)) {
            if ($ligne->getCommande() === $this) {
                $ligne->setCommande(null);
            }
            $this->total -= $ligne->getQuantite() * $ligne->getProduit()->getPrixTtc();
        }

        $this->setTotal($this->total);

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

}
