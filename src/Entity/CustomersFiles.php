<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;


#[ORM\Entity(repositoryClass: CustomersFilesRepository::class)]
class CustomersFiles
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\SecuenceGenerator(sequenceName:"upload_files_id_seq", allocationSize:1, initialValue:1)]
    #[ORM\Column(name:"id", type:"integer", nullable:false)]
    private ?int $id = null;

    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"Customers")]
    #[ORM\JoinColumn(name:"customers_id", referencedColumnName:"id")]
    #[ORM\JoinColumn(name:"customers_customer_types_id", referencedColumnName:"customer_types_id")]
    #[ORM\JoinColumn(name:"customers_identifier_types_id", referencedColumnName:"identifier_types_id")]
    private $customers;

    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"Status")]
    #[ORM\JoinColumn(name:"status_id", referencedColumnName:"id")]
    private ?Status $status;

    #[ORM\Column(length: 512, nullable: true)]
    private ?string $fileName = null;

    #[ORM\Column(length: 512, nullable: true)]
    private ?string $documentationType = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(?string $fileName): self
    {
        $this->fileName = $fileName;

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

    /**
     * Get the value of documentationType
     */ 
    public function getDocumentationType()
    {
        return $this->documentationType;
    }

    /**
     * Set the value of documentationType
     *
     * @return  self
     */ 
    public function setDocumentationType($documentationType)
    {
        $this->documentationType = $documentationType;

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