<?php

namespace App\Entity;

use App\Repository\ComptePersonnelRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ComptePersonnelRepository::class)
 */
class ComptePersonnel
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $id_Personnel;

    /**
     * @ORM\OneToOne(targetEntity=Depense::class, mappedBy="id_personnel", cascade={"persist", "remove"})
     */
    private $depense;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $occupation;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $salaire_annuel;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdPersonnel(): ?string
    {
        return $this->id_Personnel;
    }

    public function setIdPersonnel(string $id_Personnel): self
    {
        $this->id_Personnel = $id_Personnel;

        return $this;
    }

    public function getDepense(): ?Depense
    {
        return $this->depense;
    }

    public function setDepense(Depense $depense): self
    {
        // set the owning side of the relation if necessary
        if ($depense->getIdPersonnel() !== $this) {
            $depense->setIdPersonnel($this);
        }

        $this->depense = $depense;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getOccupation(): ?string
    {
        return $this->occupation;
    }

    public function setOccupation(string $occupation): self
    {
        $this->occupation = $occupation;

        return $this;
    }

    public function getSalaireAnnuel(): ?string
    {
        return $this->salaire_annuel;
    }

    public function setSalaireAnnuel(string $salaire_annuel): self
    {
        $this->salaire_annuel = $salaire_annuel;

        return $this;
    }
}
