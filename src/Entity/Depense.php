<?php

namespace App\Entity;

use App\Repository\DepenseRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\DateValidator ;
use Symfony\Component\Validator\Constraints\Date ;
use Symfony\Component\Validator\Constraints\Positive ;
use Symfony\Component\Validator\Constraints\GreaterThanValidator;

/**
 * @ORM\Entity(repositoryClass=DepenseRepository::class)
 */
class Depense
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=ComptePersonnel::class, inversedBy="depense", cascade={"persist", "remove"})
     */
    private $id_personnel;

    /**
     * @Assert\NotBlank(message="Add jpg image")
     * @Assert\File(mimeTypes={ "image/jpeg" ,"image/png","image/jpg"})
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $picture;

    /**
     * @Assert\NotBlank(message="Merci d'indiquer l'occupation relative au salarié")
     * @ORM\Column(type="string", length=255)
     */
    private $occupation;

    /**
     * @Assert\NotBlank(message="Veuillez indiquer le salaire corespondant")
     * @Assert\Positive
     * @ORM\Column(type="string", length=255)
     */
    private $salaire;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $horaire_reguliere;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $horaire_sup;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $exempte;

    /**
     *
     * @Assert\Date
     *@Assert\LessThan("today",message="date inferieur date systeme")
     * @var string A "Y-m-d" formatted value
     * @ORM\Column(type="string", length=255)
     */
    private $date_depense;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdPersonnel(): ?ComptePersonnel
    {
        return $this->id_personnel;
    }

    public function setIdPersonnel(?ComptePersonnel $id_personnel): self
    {
        $this->id_personnel = $id_personnel;

        return $this;
    }

    public function getPicture()
    {
        return $this->picture;
    }

    public function setPicture($picture)
    {
        $this->picture = $picture;

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

    public function getSalaire(): ?string
    {
        return $this->salaire;
    }

    public function setSalaire(string $salaire): self
    {
        $this->salaire = $salaire;

        return $this;
    }

    public function getHoraireReguliere(): ?string
    {
        return $this->horaire_reguliere;
    }

    public function setHoraireReguliere(string $horaire_reguliere): self
    {
        $this->horaire_reguliere = $horaire_reguliere;

        return $this;
    }

    public function getHoraireSup(): ?string
    {
        return $this->horaire_sup;
    }

    public function setHoraireSup(string $horaire_sup): self
    {
        $this->horaire_sup = $horaire_sup;

        return $this;
    }

    public function getExempte(): ?string
    {
        return $this->exempte;
    }

    public function setExempte(string $exempte): self
    {
        $this->exempte = $exempte;

        return $this;
    }

    public function getDateDepense(): ?string
    {
        return $this->date_depense;
    }

    public function setDateDepense(string $date_depense): self
    {
        $this->date_depense = $date_depense;

        return $this;
    }
}
