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
    private ?\DateTimeInterface $updateDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdDate = null;

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

    public function getUpdateDate(): ?\DateTimeInterface
    {
        return $this->updateDate;
    }

    public function setUpdateDate(?\DateTimeInterface $updateDate): self
    {
        $this->updateDate = $updateDate;

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

    public function getIdentifierTypes(): ?IdentifierTypes
    {
        return $this->identifierTypes;
    }

    public function setIdentifierTypes(?IdentifierTypes $identifierTypes): self
    {
        $this->identifierTypes = $identifierTypes;

        return $this;
    }
}