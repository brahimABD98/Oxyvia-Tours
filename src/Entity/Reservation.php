<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ReservationRepository::class)
 */
class Reservation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     *    @Assert\NotBlank (message="eerr")

     *
     */
    private $date_debut;

    /**
     * @ORM\Column(type="date")
     *    @Assert\NotBlank (message="eerr")

     *  @Assert\Expression(
     *     "this.getDateFin() >= this.getDateDebut()",
     *     message="date fin doit etre sup a date debut"
     * )
     */
    private $date_fin;

    /**
     * @ORM\Column(type="integer")
     */
    private $prix;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="veuillez remplir le nombre d'adultes")
     */
    private $nb_adulte;

    /**
     * @ORM\Column(type="string", length=255)
     *  @Assert\NotBlank (message="eerr")
     */
    private $type;






    /**
     * @ORM\Column(type="integer")
     *  @Assert\NotBlank (message="veuillez remplir le nombre d'enfants")
     */
    private $nb_enfants;




    /**
     * @ORM\Column(type="integer")
     *  @Assert\NotBlank (message="eerr")
     */
    private $NbChambreSingleReserve;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="reservation")
     */
    private $client;

    /**
     * @ORM\ManyToOne(targetEntity=Hotel::class, inversedBy="reservation")
     */
    private $hotel;

    /**
     * @ORM\Column(type="integer")
     *  @Assert\NotBlank (message="eerr")
     */
    private $nbChambreDoubleReserve;

    /**
     * @ORM\OneToMany(targetEntity=Chambre::class, mappedBy="reservation")
     */
    private $chambres;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $confirme;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $token;

    /**
     * @ORM\ManyToOne(targetEntity=Voyage::class, inversedBy="reservations")
     */
    private $voyage;

    public function __construct()
    {
        $this->chambres = new ArrayCollection();
    }





    public function getId(): ?int
    {
        return $this->id;
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

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getNbAdulte(): ?int
    {
        return $this->nb_adulte;
    }

    public function setNbAdulte(int $nb_adulte): self
    {
        $this->nb_adulte = $nb_adulte;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }







    public function getNbEnfants(): ?int
    {
        return $this->nb_enfants;
    }

    public function setNbEnfants(int $nb_enfants): self
    {
        $this->nb_enfants = $nb_enfants;

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




    public function getNbChambreSingleReserve(): ?int
    {
        return $this->NbChambreSingleReserve;
    }

    public function setNbChambreSingleReserve(int $NbChambreSingleReserve): self
    {
        $this->NbChambreSingleReserve = $NbChambreSingleReserve;

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

    public function getNbChambreDoubleReserve(): ?int
    {
        return $this->nbChambreDoubleReserve;
    }

    public function setNbChambreDoubleReserve(int $nbChambreDoubleReserve): self
    {
        $this->nbChambreDoubleReserve = $nbChambreDoubleReserve;

        return $this;
    }

    /**
     * @return Collection|Chambre[]
     */
    public function getChambres(): Collection
    {
        return $this->chambres;
    }

    public function addChambre(Chambre $chambre): self
    {
        if (!$this->chambres->contains($chambre)) {
            $this->chambres[] = $chambre;
            $chambre->setReservation($this);
        }

        return $this;
    }

    public function removeChambre(Chambre $chambre): self
    {
        if ($this->chambres->removeElement($chambre)) {
            // set the owning side to null (unless already changed)
            if ($chambre->getReservation() === $this) {
                $chambre->setReservation(null);
            }
        }

        return $this;
    }

    public function getConfirme(): ?string
    {
        return $this->confirme;
    }

    public function setConfirme(string $confirme): self
    {
        $this->confirme = $confirme;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getVoyage(): ?Voyage
    {
        return $this->voyage;
    }

    public function setVoyage(?Voyage $voyage): self
    {
        $this->voyage = $voyage;

        return $this;
    }


}
