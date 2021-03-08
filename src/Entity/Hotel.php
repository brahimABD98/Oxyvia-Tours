<?php

namespace App\Entity;

use App\Repository\HotelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HotelRepository::class)
 */
class Hotel
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
    private $nom;

    /**
     * @ORM\OneToMany(targetEntity=Chambre::class, mappedBy="hotel")
     */
    private $chambre_id;



    /**
     * @ORM\Column(type="integer")
     */
    private $nb_chambreDispo;

    /**
     * @ORM\OneToMany(targetEntity=Reservation::class, mappedBy="hotel")
     */
    private $reservation;

    public function __construct()
    {
        $this->chambre_id = new ArrayCollection();
        $this->reservations = new ArrayCollection();
        $this->reservation = new ArrayCollection();
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

    /**
     * @return Collection|Chambre[]
     */
    public function getChambreId(): Collection
    {
        return $this->chambre_id;
    }

    public function addChambreId(Chambre $chambreId): self
    {
        if (!$this->chambre_id->contains($chambreId)) {
            $this->chambre_id[] = $chambreId;
            $chambreId->setHotel($this);
        }

        return $this;
    }




    public function getNbChambreDispo(): ?int
    {
        return $this->nb_chambreDispo;
    }

    public function setNbChambreDispo(int $nb_chambreDispo): self
    {
        $this->nb_chambreDispo = $nb_chambreDispo;

        return $this;
    }

    /**
     * @return Collection|Reservation[]
     */
    public function getReservation(): Collection
    {
        return $this->reservation;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservation->contains($reservation)) {
            $this->reservation[] = $reservation;
            $reservation->setHotel($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservation->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getHotel() === $this) {
                $reservation->setHotel(null);
            }
        }

        return $this;
    }
}
