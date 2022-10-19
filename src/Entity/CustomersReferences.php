<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: CustomersReferencesRepository::class)]
class CustomersReferences
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\SecuenceGenerator(sequenceName:"references_id_seq", allocationSize:1, initialValue:1)]
    #[ORM\Column(name:"id", type:"integer", nullable:false)]
    private ?int $id=null;

    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"Customers")]
    #[ORM\JoinColumn(name:"customers_id", referencedColumnName:"id")]
    #[ORM\JoinColumn(name:"customers_customer_types_id", referencedColumnName:"customer_types_id")]
    #[ORM\JoinColumn(name:"customers_identifier_types_id", referencedColumnName:"identifier_types_id")]
    private ?Customers $customers;

    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"IdentifierTypes")]
    #[ORM\JoinColumn(name:"references_identifier_type_id", referencedColumnName:"id")]
    private ?IdentifierTypes $referencesIdentifierTypes;

    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"CountriesPhoneCode")]
    #[ORM\JoinColumn(name:"references_countries_phone_code_id", referencedColumnName:"id")]
    private ?CountriesPhoneCode $referencesCountriesPhoneCode;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $fullName = null;

    #[ORM\Column(name:"references_contact_phone", type: Types::DECIMAL, precision: 14, scale: '0', nullable: true)]
    private ?string $referencesContactPhone;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdDate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(?string $fullName): self
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getReferencesContactPhone(): ?string
    {
        return $this->referencesContactPhone;
    }

    public function setReferencesContactPhone(?string $referencesContactPhone): self
    {
        $this->referencesContactPhone = $referencesContactPhone;

        return $this;
    }

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->createdDate;
    }

    public function setCreatedDate(?\DateTimeInterface $createdDate): self
    {
        $this->createdDate = $createdDate;

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

    public function getReferencesIdentifierTypes(): ?IdentifierTypes
    {
        return $this->referencesIdentifierTypes;
    }

    public function setReferencesIdentifierTypes(?IdentifierTypes $referencesIdentifierTypes): self
    {
        $this->referencesIdentifierTypes = $referencesIdentifierTypes;

        return $this;
    }

    public function getReferencesCountriesPhoneCode(): ?CountriesPhoneCode
    {
        return $this->referencesCountriesPhoneCode;
    }

    public function setReferencesCountriesPhoneCode(?CountriesPhoneCode $referencesCountriesPhoneCode): self
    {
        $this->referencesCountriesPhoneCode = $referencesCountriesPhoneCode;

        return $this;
    }

}