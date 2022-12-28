<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: TaxesInformationRepository::class)]


class TaxesInformation
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\SecuenceGenerator(sequenceName:"taxes_information_id_seq", allocationSize:1, initialValue:1)]
    #[ORM\Column(name:"id", type:"integer", nullable:false)]
    private ?int $id;
    
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"Customers")]
    #[ORM\JoinColumn(name:"customers_id", referencedColumnName:"id")]
    #[ORM\JoinColumn(name:"customers_customer_types_id", referencedColumnName:"customer_types_id")]
    #[ORM\JoinColumn(name:"customers_identifier_types_id", referencedColumnName:"identifier_types_id")]
    private $customers;

    #[ORM\Column(type:"integer", nullable:false)]
    private ?int $dvNit;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $typePerson = null;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $granContribuyente = null;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $autorretenedor = null;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $agenteDeRetencionIVA = null;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $regimenSimple = null;
            
    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $impuestoNacionalConsumo = null;

    #[ORM\Column(type: "boolean", nullable: true)]
    private ?bool $impuestoSobreVentasIVA = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDvNit(): ?int
    {
        return $this->dvNit;
    }

    public function setDvNit(int $dvNit): self
    {
        $this->dvNit = $dvNit;

        return $this;
    }

    public function getTypePerson(): ?string
    {
        return $this->typePerson;
    }

    public function setTypePerson(?string $typePerson): self
    {
        $this->typePerson = $typePerson;

        return $this;
    }

    public function isGranContribuyente(): ?bool
    {
        return $this->granContribuyente;
    }

    public function setGranContribuyente(?bool $granContribuyente): self
    {
        $this->granContribuyente = $granContribuyente;

        return $this;
    }

    public function isAutorretenedor(): ?bool
    {
        return $this->autorretenedor;
    }

    public function setAutorretenedor(?bool $autorretenedor): self
    {
        $this->autorretenedor = $autorretenedor;

        return $this;
    }

    public function isAgenteDeRetencionIVA(): ?bool
    {
        return $this->agenteDeRetencionIVA;
    }

    public function setAgenteDeRetencionIVA(?bool $agenteDeRetencionIVA): self
    {
        $this->agenteDeRetencionIVA = $agenteDeRetencionIVA;

        return $this;
    }

    public function isRegimenSimple(): ?bool
    {
        return $this->regimenSimple;
    }

    public function setRegimenSimple(?bool $regimenSimple): self
    {
        $this->regimenSimple = $regimenSimple;

        return $this;
    }

    public function isImpuestoNacionalConsumo(): ?bool
    {
        return $this->impuestoNacionalConsumo;
    }

    public function setImpuestoNacionalConsumo(?bool $impuestoNacionalConsumo): self
    {
        $this->impuestoNacionalConsumo = $impuestoNacionalConsumo;

        return $this;
    }

    public function isImpuestoSobreVentasIVA(): ?bool
    {
        return $this->impuestoSobreVentasIVA;
    }

    public function setImpuestoSobreVentasIVA(?bool $impuestoSobreVentasIVA): self
    {
        $this->impuestoSobreVentasIVA = $impuestoSobreVentasIVA;

        return $this;
    }

    public function getCustomers(): ?Customers
    {
        return $this->customers;
    }

    public function setCustomers(?Customers $customers): self
    {
        $this->customers = $customers;

        return $this;
    }


    /**
     * Get the value of updatedDate
     */ 
    public function getUpdatedDate()
    {
        return $this->updatedDate;
    }

    /**
     * Set the value of updatedDate
     *
     * @return  self
     */ 
    public function setUpdatedDate($updatedDate)
    {
        $this->updatedDate = $updatedDate;

        return $this;
    }

    /**
     * Get the value of createdDate
     */ 
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Set the value of createdDate
     *
     * @return  self
     */ 
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }
}

