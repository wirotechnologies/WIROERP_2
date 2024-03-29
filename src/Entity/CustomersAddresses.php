<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: CustomersAddressesRepository::class)]
class CustomersAddresses
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\SecuenceGenerator(sequenceName:"customers_addresses_id_seq", allocationSize:1, initialValue:1)]
    #[ORM\Column(name:"id", type:"integer", nullable:false)]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Type(Customers::class)]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"Customers",inversedBy:"customersAddresses")]
    #[ORM\JoinColumn(name:"customers_id", referencedColumnName:"id")]
    #[ORM\JoinColumn(name:"customers_customer_types_id", referencedColumnName:"customer_types_id")]
    #[ORM\JoinColumn(name:"customers_identifier_types_id", referencedColumnName:"identifier_types_id")]
    private ?Customers $customers;

    #[Assert\NotBlank]
    #[Assert\Type(Cities::class)]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"Cities")]
    #[ORM\JoinColumn(name:"cities_id", referencedColumnName:"id")]
    private ?Cities $cities;

    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"Neighborhood")]
    #[ORM\JoinColumn(name:"neighborhood_id", referencedColumnName:"id")]
    private ?Neighborhood $neighborhood;

    #[Assert\NotBlank]
    #[Assert\Type(Status::class)]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"Status")]
    #[ORM\JoinColumn(name:"status_id", referencedColumnName:"id")]
    private ?Status $status;

    #[Assert\Length(min: 4,max: 256)]
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    #[ORM\Column(length: 256, nullable: true)]
    private ?string $line1;

    #[Assert\Type('string')]
    #[ORM\Column(length: 128, nullable: true)]
    private ?string $line2 ;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: '0', nullable: true)]
    private ?string $zipcode ;

    #[Assert\NotNull]
    #[Assert\Choice(['1','2','3','4','5','6','Comercial'])]
    #[Assert\Type('string')]
    #[ORM\Column(length: 128, nullable: true)]
    private ?string $socioeconomicStatus;

    #[ORM\Column(length: 512, nullable: true)]
    private ?string $note;
    
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdDate;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedDate ;

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

     /**
     * Get the value of socioeconomicStatus
     */ 
    public function getSocioeconomicStatus()
    {
        return $this->socioeconomicStatus;
    }

    /**
     * Set the value of socioeconomicStatus
     *
     * @return  self
     */ 
    public function setSocioeconomicStatus($socioeconomicStatus)
    {
        $this->socioeconomicStatus = $socioeconomicStatus;

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

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getCreatedDate()
    {
        return $this->createdDate->format('Y-m-d H:i:s');
    }

    public function setCreatedDate(?\DateTimeInterface $createdDate): self
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    

    public function getCities()
    {
        return $this->cities;
    }

    public function setCities(?Cities $cities): self
    {
        $this->cities = $cities;

        return $this;
    }


   

    /**
     * Get the value of neighborhood
     */ 
    public function getNeighborhood()
    {
        return $this->neighborhood;
    }

    /**
     * Set the value of neighborhood
     *
     * @return  self
     */ 
    public function setNeighborhood($neighborhood)
    {
        $this->neighborhood = $neighborhood;

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

    // public function getCustomers()
    // {
    //     return $this->customers;
    // }

    public function setCustomers(?Customers $customers): self
    {
        $this->customers = $customers;

        return $this;
    }

    /**
     * Get the value of updatedDate
     */ 
    public function getUpdatedDate()
    {
        return $this->updatedDate;
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