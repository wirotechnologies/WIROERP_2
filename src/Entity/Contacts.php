<?php

namespace App\Entity;

use App\Repository\PruebaRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
#[ORM\Entity(repositoryClass: ContactsRepository::class)]

class Contacts
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\Column(name:"id", type:"string", nullable:false, unique:true)]
    private ?string $id;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"IdentifierTypes")]
    #[ORM\JoinColumn(name:"identifier_types_id", referencedColumnName:"id")]
    private  $identifierTypes;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $firstName = null;
    
    #[ORM\Column(length: 128, nullable: true)]
    private ?string $middleName = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $secondLastName = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdDate = null;

    public function getAll()
    {
        $information = [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'middleName' => $this->middleName,
            'lastName' => $this->lastName,
            'secondLastName' => $this->secondLastName,
            'email' => $this->email,
            'identifierTypes' => $this->identifierTypes->getAll(),
        ];
        return $information;
    }

    public function getId(): ?string
    {
        return $this->id;
    }
    public function setId(string $id): string
    {
        return $this-> id = $id ;
    }

    public function setPrimaryKeys(string $id, IdentifierTypes $identifierTypes){
        $this  ->setId($id);
       $this  -> setIdentifierTypes($identifierTypes);

   }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName(?string $middleName): self
    {
        $this->middleName = $middleName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getSecondLastName(): ?string
    {
        return $this->secondLastName;
    }

    public function setSecondLastName(?string $secondLastName): self
    {
        $this->secondLastName = $secondLastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getCreatedDate()
    {
        return $this->createdDate->format('Y-m-d H:s:i');
    }

    public function setCreatedDate(?\DateTimeInterface $createdDate): self
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    public function getIdentifierTypes()
    {
        return $this->identifierTypes->getAll();
    }

    public function setIdentifierTypes(?IdentifierTypes $identifierTypes): self
    {
        $this->identifierTypes = $identifierTypes;

        return $this;
    }

    /**
     * Get the value of updatedDate
     */ 
    public function getUpdatedDate()
    {
        return $this->updatedDate->format('Y-m-d H:s:i');
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