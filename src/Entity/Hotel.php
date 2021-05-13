<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\HotelRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=HotelRepository::class)
 * @ApiResource
 */
class Hotel
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("hotel:read")
     */
    private $id;

  

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message=" this field is required ")
     */
   private $name;



    /**
     * @ORM\OneToMany(targetEntity=Reservation::class, mappedBy="hotel")
     */
    private $reservation;

    /**
     * @ORM\OneToMany(targetEntity=Voyage::class, mappedBy="hotel")
     */
    private $voyages;




    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message=" this field is required ")
     * @Groups("hotel:read")
     */
    private $pays;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message=" this field is required ")
     * @Groups("hotel:read")
     */
    private $adresse;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Range(
     *      min = 1,
     *      max = 5,
     *      notInRangeMessage = "You must be between {{ min }} and {{ max }} tall to enter",
     * )
     * @Groups("hotel:read")
     */
    private $nbetoile;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Length(
     *      min = 8,
     *      max = 8,
     *      minMessage = "Your first name must be at least {{ limit }} characters long",
     *      maxMessage = "Your first name cannot be longer than {{ limit }} characters"
     * )
     * @Groups("hotel:read")
     */
    private $num;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(message="this field should be a valid mail ")
     * @Groups("hotel:read")
     */

    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("hotel:read")
     */
    private $image;


    /**
     * @ORM\OneToMany(targetEntity=Chambre::class, mappedBy="idhotel")
     * @Groups("hotel:read")
     */
    private $idchambre;

    /**
     * @ORM\Column(type="float",nullable=true)
     * @Groups("hotel:read")
     */
    private $lat;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups("hotel:read")
     */
    private $lng;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="idhotel")
     * @Groups("hotel:read")
     */
    private $idcomment;


    public function __construct()
    {
       
        $this->reservations = new ArrayCollection();
        $this->reservation = new ArrayCollection();
        $this->voyages = new ArrayCollection();
        $this->idchambre = new ArrayCollection();
        $this->idcomment = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(string $pays): self
    {
        $this->pays = $pays;

        return $this;
    }





    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getNbetoile(): ?int
    {
        return $this->nbetoile;
    }

    public function setNbetoile(int $nbetoile): self
    {
        $this->nbetoile = $nbetoile;

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
    }
    public function getNum(): ?int
    {
        return $this->num;
    }

    public function setNum(int $num): self
    {
        $this->num = $num;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

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
     * @return Collection|Chambre[]
     */
    public function getIdchambre(): Collection
    {
        return $this->idchambre;
    }

    public function addIdchambre(Chambre $idchambre): self
    {
        if (!$this->idchambre->contains($idchambre)) {
            $this->idchambre[] = $idchambre;
            $idchambre->setIdhotel($this);
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
    }
    public function removeIdchambre(Chambre $idchambre): self
    {
        if ($this->idchambre->removeElement($idchambre)) {
            // set the owning side to null (unless already changed)
            if ($idchambre->getIdhotel() === $this) {
                $idchambre->setIdhotel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Voyage[]
     */
    public function getVoyages(): Collection
    {
        return $this->voyages;
    }

    public function addVoyage(Voyage $voyage): self
    {
        if (!$this->voyages->contains($voyage)) {
            $this->voyages[] = $voyage;
            $voyage->setHotel($this);
        }
    }
    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(?float $lat): self
    {
        $this->lat = $lat;

        return $this;
    }

    public function getLng(): ?float
    {
        return $this->lng;
    }

    public function setLng(?float $lng): self
    {
        $this->lng = $lng;

        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getIdcomment(): Collection
    {
        return $this->idcomment;
    }

    public function addIdcomment(Comment $idcomment): self
    {
        if (!$this->idcomment->contains($idcomment)) {
            $this->idcomment[] = $idcomment;
            $idcomment->setIdhotel($this);
        }

        return $this;
    }

    public function removeVoyage(Voyage $voyage): self
    {
        if ($this->voyages->removeElement($voyage)) {
            // set the owning side to null (unless already changed)
            if ($voyage->getHotel() === $this) {
                $voyage->setHotel(null);
            }
        }
    }
    public function removeIdcomment(Comment $idcomment): self
    {
        if ($this->idcomment->removeElement($idcomment)) {
            // set the owning side to null (unless already changed)
            if ($idcomment->getIdhotel() === $this) {
                $idcomment->setIdhotel(null);
            }
        }

        return $this;
    }


}
