<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: CustomersPhonesRepository::class)]
class CustomersPhones
{ 
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\SecuenceGenerator(sequenceName:"customers_phones_id_seq", allocationSize:1, initialValue:1)]
    #[ORM\Column(name:"id", type:"integer", nullable:false)]
    private ?int $id = null;

    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"PhonesNumbers")]
    #[ORM\JoinColumn(name:"phones_numbers_phone_number", referencedColumnName:"phone_number")]
    private ?PhonesNumbers $phonesNumber;

    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"CountriesPhoneCode")]
    #[ORM\JoinColumn(name:"countries_phone_code_id", referencedColumnName:"id")]
    private ?CountriesPhoneCode $countriesPhoneCode;

    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"Customers")]
    #[ORM\JoinColumn(name:"customers_id", referencedColumnName:"id")]
    #[ORM\JoinColumn(name:"customers_customer_types_id", referencedColumnName:"customer_types_id")]
    #[ORM\JoinColumn(name:"customers_identifier_types_id", referencedColumnName:"identifier_types_id")]
    private ?Customers $customers;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdDate = null;


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

    public function getCountriesPhoneCode(): ?CountriesPhoneCode
    {
        return $this->countriesPhoneCode;
    }

    public function setCountriesPhoneCode(?CountriesPhoneCode $countriesPhoneCode): self
    {
        $this->countriesPhoneCode = $countriesPhoneCode;

        return $this;
    }


    /**
     * Get the value of phonesNumber
     */ 
    public function getPhonesNumber()
    {
        return $this->phonesNumber;
    }

    /**
     * Set the value of phonesNumber
     *
     * @return  self
     */ 
    public function setPhonesNumber($phonesNumber)
    {
        $this->phonesNumber = $phonesNumber;

        return $this;
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */ 
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}