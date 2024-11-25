<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiProperty;

/**
 * @ApiResource
 * @ORM\Entity(repositoryClass="App\Repository\ProduitRepository")
 */


class Produit
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @ApiProperty(example="Produit 1")
     */
    private $titre;

    /**
     * @ORM\Column(type="text")
     * @ApiProperty(example="Une description de produit.")
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     * @ApiProperty(example=10)
     */
    private $quantiteStock;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Marque", inversedBy="produits", cascade={"persist"})
     * @ORM\JoinColumn(name="marque_id", referencedColumnName="id",nullable=true)
     */
    private $marque;

    /**
     * @ORM\Column(type="float")
     * @ApiProperty(example=100.0)
     */
    private $prixTtc;

    /**
     * @ORM\Column(type="string", length=100)
     * @ApiProperty(example="Type 1")
     */
    private $types;

    /**
     * @ORM\Column(type="string", length=100)
     * @ApiProperty(example="Genre 1")
     */
    private $genre;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LigneCommande", mappedBy="produit", cascade={"persist"})
     */
    private $lignes;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getQuantiteStock(): ?int
    {
        return $this->quantiteStock;
    }

    public function setQuantiteStock(int $quantiteStock): self
    {
        $this->quantiteStock = $quantiteStock;
        return $this;
    }
    /**
     * Cette méthode expose l'ID de la marque dans Swagger
     * @ApiProperty(example=1)
     */
    public function getMarque_id(): ?int
    {
        return $this->marque ? $this->marque->getId() : null;
    }

    /**
     * Masquer la relation `marque` dans Swagger.
     * Utiliser `@ApiProperty` pour faire en sorte que Swagger n'affiche que `marque_id`.
     * 
     * @ApiProperty(readable=false, writable=false)
     */
    public function getMarque(): ?Marque
    {
        return $this->marque;
    }

    public function setMarque(Marque $marque): self
    {
        $this->marque = $marque;
        return $this;
    }

    /**
     * Méthode pour définir l'ID de la marque.
     * Utilisé pour la création ou modification de produit (par exemple, dans l'API).
     */
    public function setMarque_id(int $marque_id): self
    {
        // Trouver la marque par son ID et l'assigner
        $this->marque = $marque_id ? $marque_id : null;
        return $this;
    }

    public function getPrixTtc(): ?float
    {
        return $this->prixTtc;
    }

    public function setPrixTtc(float $prixTtc): self
    {
        $this->prixTtc = $prixTtc;
        return $this;
    }

    public function setTypes(string $types): self
    {
        $this->types = $types;

        return $this;
    }

    public function getTypes(): ?string
    {
        return $this->types;
    }

    public function getTypesArray(): array
    {
        if (is_string($this->types)) {
            return explode(',', $this->types);  // Convertir en tableau si c'est une chaîne
        }

        if (is_array($this->types)) {
            return $this->types;
        }
        return [];
    }

    /**
     * Ne pas exposer cette méthode dans la documentation Swagger.
     *
     * @ApiProperty(readable=false, writable=false)
     */
    public function setTypesFromArray($types)
    {
        // Si $types est une chaîne de caractères, on la convertit en tableau
        if (is_string($types)) {
            $types = explode(',', $types);
        }

        if (!is_array($types)) {
            throw new \InvalidArgumentException('Expected an array for types.');
        }

        $this->types = $types;

        return $this;
    }


    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): self
    {
        $this->genre = $genre;
        return $this;
    }

    public function ajouterStock(int $quantite): self
    {
        $this->quantiteStock += $quantite;
        return $this;
    }

    public function retirerStock(int $quantite): self
    {
        if ($this->quantiteStock >= $quantite) {
            $this->quantiteStock -= $quantite;
        } else {
            throw new \Exception("Quantité insuffisante en stock.");
        }
        return $this;
    }
}
