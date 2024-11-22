<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class LigneCommande
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Produit", inversedBy="lignes")
     * @ORM\JoinColumn(name="produit_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    private $produit;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Commande", inversedBy="lignes")
     * @ORM\JoinColumn(name="commande_id", referencedColumnName="id", nullable=false)
     */
    private $commande;


    /**
     * @ORM\Column(type="integer")
     */
    private $quantite;

    public function __construct(Produit $produit, Commande $commande, int $quantite = 1)
    {
        $this->produit = $produit;
        $this->commande = $commande;
        $this->quantite = $quantite;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduit(): ?Produit
    {
        return $this->produit;
    }

    public function setProduit(Produit $produit): self
    {
        $this->produit = $produit;

        return $this;
    }

    public function getCommande(): ?Commande
    {
        return $this->commande;
    }

    public function setCommande(?Commande $commande): self
    {
        $this->commande = $commande;

        return $this;
    }

    public function getQuantite(): ?int
    {
        return $this->quantite;
    }

    public function setQuantite(int $quantite): self
    {
        $this->quantite = $quantite;

        return $this;
    }
}
