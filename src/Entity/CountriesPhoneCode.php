<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: CountriesPhoneCodeRepository::class)]
class CountriesPhoneCode
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\SecuenceGenerator(sequenceName:"countries_phone_code_id_seq", allocationSize:1, initialValue:1)]
    #[ORM\Column(name:"id", type:"integer", nullable:false)]
    private ?int $id;


    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"Countries")]
    #[ORM\JoinColumn(name:"countries_id", referencedColumnName:"id")]
    private ?Countries $countries;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: '0', nullable: true)]
    private ?string $code = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getCountries(): ?Countries
    {
        return $this->countries;
    }

    public function setCountries(?Countries $countries): self
    {
        $this->countries = $countries;

        return $this;
    }



}    