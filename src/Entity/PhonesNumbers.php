<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: PhonesNumbersRepository::class)]
class PhonesNumbers
{ 

  

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\Column(name:"phone_number", type: Types::DECIMAL, precision: 14, scale: '0', nullable: true)]
    private ?string $phoneNumber;
    
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"CountriesPhoneCode")]
    #[ORM\JoinColumn(name:"countries_phone_code_id", referencedColumnName:"id")]
    private ?CountriesPhoneCode $countriesPhoneCode;
    
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdDate = null;

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
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
     * Set the value of phoneNumber
     *
     * @return  self
     */ 
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }
}