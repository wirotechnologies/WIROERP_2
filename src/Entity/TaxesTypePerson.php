<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
#[ORM\Entity(repositoryClass: TaxesTypePersonRepository::class)]
class TaxesTypePerson
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\SecuenceGenerator(sequenceName:"taxes_type_person_id_seq", allocationSize:1, initialValue:1)]
    #[ORM\Column(name:"id", type:"integer", nullable:false)]
    private ?int $id;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $typePerson = null;

    public function getId(): ?int
    {
        return $this->id;
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
}