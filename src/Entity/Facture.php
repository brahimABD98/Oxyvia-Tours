<?php

namespace App\Entity;

use App\Repository\FactureRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;

/**
 * @ORM\Entity(repositoryClass=FactureRepository::class)
 */
class Facture
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez indiquer un identifiant ou un numéro de passeport")
     * @Assert\Length(
     *   min=8,
     *   max=9,
     *   minMessage="Votre identifiant doit contenir au moins {{ limit }} cacactères",
     *   maxMessage="Votre numéro de passeport ne doit pas contenir plus de {{ limit }} caractères"
     * )

     */
    private $identifiant;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez indiquer le montant à payer")
     */
    private $montant;

    /**
     * @ORM\Column(type="date",nullable=false)
     */
    private $date_paiement;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez indiquer la devise de votre pays")
     */
    private $devise;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez choisir le moyen de votre paiement")
     */
    private $moyen_paiement;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez selectionner le mode de votre paiement")
     */
    private $mode_paiement;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $typeCB;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Ncb;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $code_securite;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date_expiration;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $location;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdentifiant(): ?string
    {
        return $this->identifiant;
    }

    public function setIdentifiant(string $identifiant): self
    {
        $this->identifiant = $identifiant;

        return $this;
    }

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDatePaiement(): ?\DateTimeInterface
    {
        return $this->date_paiement;
    }

    public function setDatePaiement(\DateTimeInterface $date_paiement): self
    {
        $this->date_paiement = $date_paiement;

        return $this;
    }

    public function getDevise(): ?string
    {
        return $this->devise;
    }

    public function setDevise(string $devise): self
    {
        $this->devise = $devise;

        return $this;
    }

    public function getMoyenPaiement(): ?string
    {
        return $this->moyen_paiement;
    }

    public function setMoyenPaiement(string $moyen_paiement): self
    {
        $this->moyen_paiement = $moyen_paiement;

        return $this;
    }

    public function getModePaiement(): ?string
    {
        return $this->mode_paiement;
    }

    public function setModePaiement(string $mode_paiement): self
    {
        $this->mode_paiement = $mode_paiement;

        return $this;
    }

    public function getTypeCB(): ?string
    {
        return $this->typeCB;
    }

    public function setTypeCB(?string $typeCB): self
    {
        $this->typeCB = $typeCB;

        return $this;
    }

    public function getNcb(): ?string
    {
        return $this->Ncb;
    }

    public function setNcb(?string $Ncb): self
    {
        $this->Ncb = $Ncb;

        return $this;
    }

    public function getCodeSecurite(): ?string
    {
        return $this->code_securite;
    }

    public function setCodeSecurite(?string $code_securite): self
    {
        $this->code_securite = $code_securite;

        return $this;
    }

    public function getDateExpiration(): ?\DateTimeInterface
    {
        return $this->date_expiration;
    }

    public function setDateExpiration(?\DateTimeInterface $date_expiration): self
    {
        $this->date_expiration = $date_expiration;

        return $this;
    }

    public function getLocation(): ?string
    {
        return $this->location;
    }

    public function setLocation(?string $location): self
    {
        $this->location = $location;

        return $this;
    }
}
