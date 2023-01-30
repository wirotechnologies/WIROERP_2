<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: CustomersReferencesRepository::class)]
class CustomersReferences
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\SecuenceGenerator(sequenceName:"references_id_seq", allocationSize:1, initialValue:1)]
    #[ORM\Column(name:"id", type:"integer", nullable:false)]
    private ?int $id=null;

    #[Assert\NotBlank]
    #[Assert\Type(Customers::class)]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"Customers")]
    #[ORM\JoinColumn(name:"customers_id", referencedColumnName:"id")]
    #[ORM\JoinColumn(name:"customers_customer_types_id", referencedColumnName:"customer_types_id")]
    #[ORM\JoinColumn(name:"customers_identifier_types_id", referencedColumnName:"identifier_types_id")]
    private ?Customers $customers;

    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"IdentifierTypes")]
    #[ORM\JoinColumn(name:"identifier_types_id", referencedColumnName:"id")]
    private ?IdentifierTypes $referencesIdentifierTypes;

    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"CountriesPhoneCode")]
    #[ORM\JoinColumn(name:"countries_phone_code_id", referencedColumnName:"id")]
    private ?CountriesPhoneCode $referencesCountriesPhoneCode;

    #[Assert\NotBlank]
    #[Assert\Type(Status::class)]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"Status")]
    #[ORM\JoinColumn(name:"status_id", referencedColumnName:"id")]
    private ?Status $status;

    #[Assert\NotBlank]
    #[Assert\Choice(['Familiar','Personal','Personal del Representante Legal'])]
    #[Assert\Type('string')]
    #[ORM\Column(length: 128, nullable: true)]
    private ?string $typeReference = null;

    #[Assert\Length(min: 5,max: 50)]
    #[Assert\Type('string')]
    #[Assert\NotBlank]
    #[ORM\Column(length: 128, nullable: true)]
    private ?string $fullName = null;

    #[Assert\Length(min: 7,max: 10)]
    #[Assert\NotBlank] 
    #[ORM\Column(name:"phone_number", type: Types::DECIMAL, precision: 14, scale: '0', nullable: true)]
    private ?string $phoneNumber;

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

    public function getReferencesCountriesPhoneCode(): ?CountriesPhoneCode
    {
        return $this->referencesCountriesPhoneCode;
    }

    public function setReferencesCountriesPhoneCode(?CountriesPhoneCode $referencesCountriesPhoneCode): self
    {
        $this->referencesCountriesPhoneCode = $referencesCountriesPhoneCode;

        return $this;
    }

    /**
     * Get the value of phoneNumber
     */ 
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set the value of phoneNumber
     *
     * @return  self
     */ 
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get the value of typeReference
     */ 
    public function getTypeReference()
    {
        return $this->typeReference;
    }

    /**
     * Set the value of typeReference
     *
     * @return  self
     */ 
    public function setTypeReference($typeReference)
    {
        $this->typeReference = $typeReference;

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
}