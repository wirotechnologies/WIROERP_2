<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: PhonesNumbersRepository::class)]
class PhonesNumbers
{
    #[Assert\Length(min: 7,max: 10)]
    #[Assert\NotBlank] 
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\Column(name:"phone_number", type: Types::DECIMAL, precision: 14, scale: '0', nullable: true)]
    private ?string $phoneNumber;
    
    #[Assert\NotBlank]
    #[Assert\Type(CountriesPhoneCode::class)]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"CountriesPhoneCode")]
    #[ORM\JoinColumn(name:"countries_phone_code_id", referencedColumnName:"id")]
    private  $countriesPhoneCode;
   

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPrimaryKeys(string $phoneNumber, CountriesPhoneCode $countriesPhoneCode)
    {
        $this->setPhoneNumber($phoneNumber);
        $this-> setCountriesPhoneCode($countriesPhoneCode);
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