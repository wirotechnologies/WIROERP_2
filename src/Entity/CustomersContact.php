<?php
namespace App\Entity;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: CustomersContactRepository::class)]

class CustomersContact
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"IDENTITY")]
    #[ORM\SecuenceGenerator(sequenceName:"customers_contact_id_seq", allocationSize:1, initialValue:1)]
    #[ORM\Column(name:"id", type:"integer", nullable:false)]
    private ?int $id;
    
    #[Assert\NotBlank]
    #[Assert\Type(Customers::class)]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"Customers")]
    #[ORM\JoinColumn(name:"customers_id", referencedColumnName:"id")]
    #[ORM\JoinColumn(name:"customers_customer_types_id", referencedColumnName:"customer_types_id")]
    #[ORM\JoinColumn(name:"customers_identifier_types_id", referencedColumnName:"identifier_types_id")]
    private $customers;

    #[Assert\NotBlank]
    #[Assert\Type(Contacts::class)]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"Contacts")]
    #[ORM\JoinColumn(name:"contacts_id", referencedColumnName:"id")]
    #[ORM\JoinColumn(name:"contacts_identifier_types_id", referencedColumnName:"identifier_types_id")]
    private  $contacts;

    #[Assert\NotBlank]
    #[Assert\Type(Status::class)]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"Status")]
    #[ORM\JoinColumn(name:"status_id", referencedColumnName:"id")]
    private ?Status $status;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContacts()
    {
        return $this->contacts->getAll();
    }

    public function setContacts(?Contacts $contacts): self
    {
        $this->contacts = $contacts;

        return $this;
    }

    // public function getCustomers(): ?Customers
    // {
    //     return $this->customers;
    // }

    public function setCustomers(?Customers $customers): self
    {
        $this->customers = $customers;

        return $this;
    }

    /**
     * Get the value of status
     */ 
    public function getStatus()
    {
        return $this->status->getStatus();
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