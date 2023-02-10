<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: StatusRepository::class)]
class Status
//This class represents the status of customers address and customers phones, customersFiles customersContact, customers references,. 1=Active o 2=Inactive.
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\SecuenceGenerator(sequenceName:"status_id_seq", allocationSize:1, initialValue:1)]
    #[ORM\Column(name:"id", type:"integer", nullable:false)]
    private ?int $id;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): self
    {
        $this->status = $status;

        return $this;
    }
}