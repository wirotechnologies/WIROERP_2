<?php

namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity(repositoryClass: NeighborhoodRepository::class)]
class Neighborhood
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\SecuenceGenerator(sequenceName:"neighborhood_id_seq", allocationSize:1, initialValue:1)]
    #[ORM\Column(name:"id", type:"integer", nullable:false)]
    private ?int $id;

    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"Cities")]
    #[ORM\JoinColumn(name:"cities_id", referencedColumnName:"id")]
    private ?Cities $cities;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $name = null;
}