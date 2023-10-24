<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: CustomersPhonesRepository::class)]
class CustomersPhones
{ 
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\SecuenceGenerator(sequenceName:"customers_phones_id_seq", allocationSize:1, initialValue:1)]
    #[ORM\Column(name:"id", type:"integer", nullable:false)]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Type(PhonesNumbers::class)]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"PhonesNumbers")]
    #[ORM\JoinColumn(name:"phones_numbers_phone_number", referencedColumnName:"phone_number")]
    #[ORM\JoinColumn(name:"phones_numbers_countries_phone_code_id", referencedColumnName:"countries_phone_code_id")]
    private ?PhonesNumbers $phonesNumber;

    #[Assert\NotBlank]
    #[Assert\Type(Customers::class)]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"Customers")]
    #[ORM\JoinColumn(name:"customers_id", referencedColumnName:"id")]
    #[ORM\JoinColumn(name:"customers_customer_types_id", referencedColumnName:"customer_types_id")]
    #[ORM\JoinColumn(name:"customers_identifier_types_id", referencedColumnName:"identifier_types_id")]
    private ?Customers $customers;

    #[Assert\NotBlank]
    #[Assert\Type(Status::class)]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"Status")]
    #[ORM\JoinColumn(name:"status_id", referencedColumnName:"id")]
    private ?Status $status;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdDate = null;


    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedDate ;

    // public function getCustomers(): ?Customers
    // {
    //     return $this->customers;
    // }

    public function setCustomers(?Customers $customers): self
    {
        $this->customers = $customers;

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
     * Get the value of status
     */ 
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @return  self
     */ 
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function getCreatedDate()
    {
        return $this->createdDate->format('Y-m-d H:i:s');
    }

    public function setCreatedDate(?\DateTimeInterface $createdDate): self
    {
        $this->createdDate = $createdDate;

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
}