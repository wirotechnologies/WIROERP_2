<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;


#[ORM\Entity(repositoryClass: CustomersAddressesRepository::class)]
class CustomersAddresses
{
    //Quien es id? = referencedColumnName:"customers_type_id"
    //Falta idCustomerTypesCustomers
    //Se agrego $socieconomicStatus int , ¿type: Types::DECIMAL?
    //Para probar image type blob ¿type: Types::BLOB?

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\SecuenceGenerator(sequenceName:"customers_addresses_id_seq", allocationSize:1, initialValue:1)]
    #[ORM\Column(name:"id", type:"integer", nullable:false)]
    private ?int $id = null;

    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"Customers")]
    #[ORM\JoinColumn(name:"customers_id", referencedColumnName:"id")]
    #[ORM\JoinColumn(name:"customers_customer_types_id", referencedColumnName:"customer_types_id")]
    #[ORM\JoinColumn(name:"customers_identifier_types_id", referencedColumnName:"identifier_types_id")]
    private ?Customers $customers;

    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"Cities")]
    #[ORM\JoinColumn(name:"cities_id", referencedColumnName:"id")]
    private ?Cities $cities;

    #[ORM\Column(length: 256, nullable: true)]
    private ?string $line1;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $line2 ;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: '0', nullable: true)]
    private ?string $zipcode ;

    #[ORM\Column(type: Types::DECIMAL, nullable: true)]
    private ?int $socieconomicStatus;

     /**
     * @var string
     *
     * @ORM\Column(name="image", type="blob", nullable=true)
     */
    private $image;

    #[ORM\Column(length: 512, nullable: true)]
    private ?string $note ;
    
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdDate ;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLine1(): ?string
    {
        return $this->line1;
    }

    public function setLine1(?string $line1): self
    {
        $this->line1 = $line1;

        return $this;
    }

    public function getLine2(): ?string
    {
        return $this->line2;
    }

    public function setLine2(?string $line2): self
    {
        $this->line2 = $line2;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(?string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getSocieconomicStatus(): ?string
    {
        return $this->socieconomicStatus;
    }

    public function setSocieconomicStatus(?string $socieconomicStatus): self
    {
        $this->socieconomicStatus = $socieconomicStatus;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

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

    public function getCities(): ?Cities
    {
        return $this->cities;
    }

    public function setCities(?Cities $cities): self
    {
        $this->cities = $cities;

        return $this;
    }

   
   
}