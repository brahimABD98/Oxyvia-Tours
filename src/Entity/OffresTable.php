<?php

namespace App\Entity;

use App\Repository\OffresRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=OffresRepository::class)
 */
class OffresTable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("offre:read")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="this field is required")
     * @Groups("offre:read")
     */
    private $IdOffres;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="this field is required")
     * @Groups("offre:read")
     */
    private $nom;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank(message="this field is required")
     * @Assert\GreaterThan("today")
     * @Groups("offre:read")
     */
    private $date_debut;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotBlank(message="this field is required")
     * @Groups("offre:read")
     */
    private $date_fin;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(message="this field is required")
     * @Groups("offre:read")
     */
    private $sujet;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="this field is required")
     * @Groups("offre:read")
     */
    private $prix;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdOffres(): ?int
    {
        return $this->IdOffres;
    }

    public function setIdOffres(int $IdOffres): self
    {
        $this->IdOffres = $IdOffres;

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

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface  $date_debut): self
    {
        $this->date_debut = $date_debut;

        return $this;
    }
    public function getdate_debut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }


    public function setdate_debut(\DateTimeInterface  $date_debut): self
    {
        $this->date_debut = $date_debut;

        return $this;
    }
    public function getDateFin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setDateFin(\DateTimeInterface $date_fin): self
    {
        $this->date_fin = $date_fin;

        return $this;
    }


    public function getdate_fin(): ?\DateTimeInterface
    {
        return $this->date_fin;
    }

    public function setdate_fin(\DateTimeInterface $date_fin): self
    {
        $this->date_fin = $date_fin;

        return $this;
    }

    public function getSujet(): ?string
    {
        return $this->sujet;
    }

    public function setSujet(string $sujet): self
    {
        $this->sujet = $sujet;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): self
    {
        $this->prix = $prix;

        return $this;
    }
}
