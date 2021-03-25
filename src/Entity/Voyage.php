<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use App\Repository\VoyageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity(repositoryClass=VoyageRepository::class)
 */
class Voyage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="veuillez remplir le nom")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="veuillez remplir la ville")
     */
    private $ville;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="veuillez remplir la description")
     */
    private $description;

    /**
     * @ORM\Column(type="date",nullable=true)

     * @Assert\NotBlank (message="eerr")
     *

     */
    public $date_debut;

    /**
     * @ORM\Column(type="date",nullable=true)
     * @Assert\NotBlank (message="eerr")

     *  @Assert\Expression(
     *     "this.getDateFin() >= this.getDateDebut()",
     *     message="date fin doit etre sup a date debut"
     * )
     *
     *
     */
    public $date_fin;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="veuillez remplir prix d'un personne")

     */
    public $prix_personne;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="veuillez remplir le nombre de personne ")
     *
     *
     */
    public $nb_personne;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;



    /**
     * @ORM\ManyToMany(targetEntity=Place::class, inversedBy="voyages",cascade={"persist"})
     */
    private $place;

    /**
     * @ORM\ManyToOne(targetEntity=Hotel::class, inversedBy="voyages")
     * @Assert\NotBlank(message="veuillez remplir l'hotel")

     */
    private $hotel;

    /**
     * @ORM\OneToMany(targetEntity=Transport::class, mappedBy="voyage")
     */
    private $transport;

    /**
     * @ORM\OneToMany(targetEntity=Reservation::class, mappedBy="voyage")
     */
    private $reservations;



    public function __construct()
    {
        $this->place = new ArrayCollection();
        $this->transport = new ArrayCollection();
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

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

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->date_debut;
    }

    public function setDateDebut(\DateTimeInterface $date_debut): self
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

    public function getPrixPersonne(): ?int
    {
        return $this->prix_personne;
    }

    public function setPrixPersonne(int $prix_personne): self
    {
        $this->prix_personne = $prix_personne;

        return $this;
    }

    public function getNbPersonne(): ?int
    {
        return $this->nb_personne;
    }

    public function setNbPersonne(int $nb_personne): self
    {
        $this->nb_personne = $nb_personne;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }



    /**
     * @return Collection|Place[]
     */
    public function getPlace(): Collection
    {
        return $this->place;
    }

    public function addPlace(Place $place): self
    {
        if (!$this->place->contains($place)) {
            $this->place[] = $place;
        }

        return $this;
    }

    public function removePlace(Place $place): self
    {
        $this->place->removeElement($place);

        return $this;
    }

    public function getHotel(): ?Hotel
    {
        return $this->hotel;
    }

    public function setHotel(?Hotel $hotel): self
    {
        $this->hotel = $hotel;

        return $this;
    }

    /**
     * @return Collection|Transport[]
     */
    public function getTransport(): Collection
    {
        return $this->transport;
    }

    public function addTransport(Transport $transport): self
    {
        if (!$this->transport->contains($transport)) {
            $this->transport[] = $transport;
            $transport->setVoyage($this);
        }

        return $this;
    }

    public function removeTransport(Transport $transport): self
    {
        if ($this->transport->removeElement($transport)) {
            // set the owning side to null (unless already changed)
            if ($transport->getVoyage() === $this) {
                $transport->setVoyage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Reservation[]
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setVoyage($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getVoyage() === $this) {
                $reservation->setVoyage(null);
            }
        }

        return $this;
    }




}
