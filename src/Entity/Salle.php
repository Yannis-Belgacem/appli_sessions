<?php

namespace App\Entity;

use App\Entity\Session;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\SalleRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=SalleRepository::class)
 */
class Salle
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $numero;

    /**
     * @ORM\Column(type="integer")
     */
    private $capacite;

    /**
     * @ORM\ManyToOne(targetEntity=Centre::class, inversedBy="salles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $centre;

    /**
     * @ORM\OneToOne(targetEntity=Session::class, mappedBy="salle", cascade={"persist", "remove"})
     */
    private $session;

    public function __construct()
    {
        $this->session = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getCapacite(): ?int
    {
        return $this->capacite;
    }

    public function setCapacite(int $capacite): self
    {
        $this->capacite = $capacite;

        return $this;
    }

    public function getCentre(): ?Centre
    {
        return $this->centre;
    }

    public function setCentre(?Centre $centre): self
    {
        $this->centre = $centre;

        return $this;
    }

    public function getSession(): ?Session
    {
        return $this->session;
    }

    public function setSession(Session $session): self
    {
        // set the owning side of the relation if necessary
        if ($session->getSalle() !== $this) {
            $session->setSalle($this);
        }

        $this->session = $session;

        return $this;
    }

}
