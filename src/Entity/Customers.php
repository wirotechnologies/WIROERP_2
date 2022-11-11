<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Contraints as Assert;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[ORM\Entity(repositoryClass: CustomersRepository::class)]
class Customers
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\Column(name:"id", type:"string", nullable:false)]
    private ?string $id;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"CustomerTypes")]
    #[ORM\JoinColumn(name:"customer_types_id", referencedColumnName:"id")]
    private $customerTypes;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"IdentifierTypes")]
    #[ORM\JoinColumn(name:"identifier_types_id", referencedColumnName:"id")]
    private  $identifierTypes;

    #[ORM\Column(type: "string", length: 128, nullable: true)]
    private ?string $commercialName = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $firstName = null;
    
    #[ORM\Column(length: 128, nullable: true)]
    private ?string $middleName = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $lastName = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $secondLastName = null;

    #[ORM\Column(length: 128, nullable: true)]
    private ?string $email = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdDate = null;

    public function getAll($customerPhones, $customerAddress, $customerReferences, $customerContacts)
    {
       
        if($customerContacts == []){
            $contactsArray = Null;
        }
        else{
            $contactsArray = [];
            $identificationContact = [];
            foreach($customerContacts as $customerContact){
                $contentIdentificationContact = ['value'=>$customerContact->getContacts()->getId(), 'idIdentifierType'=>$customerContact->getContacts()->getIdentifierTypes()->getId()];
                array_push($identificationContact,$contentIdentificationContact);
            $contentContact = [
                'firstName'=>$customerContact->getContacts()->getFirstName(),
                'middleName'=>$customerContact->getContacts()->getMiddleName(),
                'lastName'=>$customerContact->getContacts()->getLastName(),
                'secondLastName'=>$customerContact->getContacts()->getSecondLastName(),
                'email'=>$customerContact->getContacts()->getEmail(),
                'identification'=>$identificationContact,

            ];
            array_push($contactsArray,$contentContact);
            }
        }
        
        $phoneNumberArray = [];
        foreach($customerPhones as $customerPhone){
            array_push($phoneNumberArray, $customerPhone->getPhonesNumber()->getPhoneNumber());
        }

        $referencesArray = [];
        foreach($customerReferences as $customerReference){
            $contentReference = ['fullName'=>$customerReference->getFullName(),'type'=> $customerReference->getReferencesIdentifierTypes()->getId(), 'contactPhone'=>$customerReference->getPhoneNumber()];
            array_push($referencesArray,$contentReference);
        }

        $addressArray = [
            'line1'=>$customerAddress->getLine1(),
            'line2'=>$customerAddress->getLine2(),
            'zipcode'=>$customerAddress->getZipcode(),
            'note'=>$customerAddress->getNote(),
            'city'=>$customerAddress->getCities()->getName(),
            'socioeconomicStatus'=>$customerAddress->getSocioeconomicStatus()
        ];
            
        $information = [
            'id'=> $this->id,
            'customerTypes'=> $this->customerTypes->getId(),
            'identifierTypes'=> $this->identifierTypes->getId(),
            'commercialName'=> $this->commercialName,
            'mainContact'=>$contactsArray,
            'firstName'=>$this->firstName,
            'middleName'=>$this->middleName,
            'lastName'=>$this->lastName,
            'secondLastName'=>$this->secondLastName,
            'email'=>$this->email,
            'phoneNumber'=>$phoneNumberArray,
            'address'=>$addressArray,
            'references'=>$referencesArray
        ];  

        return $information;      
    }

    public function getAllByRetrieve($customerPhones, $customerContract)
    {
        $phoneNumberArray = [];
        foreach($customerPhones as $customerPhone){
            array_push($phoneNumberArray, $customerPhone->getPhonesNumber()->getPhoneNumber());
        }
        
        $information = [
            'id'=> $this->id,
            'customerTypes'=> $this->customerTypes->getId(),
            'identifierTypes'=> $this->identifierTypes->getId(),
            'commercialName'=> $this->commercialName,
            'firstName'=>$this->firstName,
            'middleName'=>$this->middleName,
            'lastName'=>$this->lastName,
            'secondLastName'=>$this->secondLastName,
            'email'=>$this->email,
            'phoneNumber'=>$phoneNumberArray,
            'contractId'=>$customerContract['IdContract'],
            'balance'=> $customerContract['Balance'],
            'status'=>$customerContract['Status']
        ];         
        return $information;   
    }

    public function getByExpressionRetrieveCustomer($customerPhones)
    {
        $phoneNumberArray = [];
        foreach($customerPhones as $customerPhone){
            array_push($phoneNumberArray, $customerPhone->getPhonesNumber()->getPhoneNumber());
        }

        $information = [
            'id'=> $this->id,
            'customerTypes'=> $this->customerTypes->getId(),
            'identifierTypes'=> $this->identifierTypes->getId(),
            'commercialName'=> $this->commercialName,
            'firstName'=>$this->firstName,
            'middleName'=>$this->middleName,
            'lastName'=>$this->lastName,
            'secondLastName'=>$this->secondLastName,
            'email'=>$this->email,
            'phoneNumber'=>$phoneNumberArray
        ];
        return $information;

    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): string
    {
        return $this-> id = $id ;
    }

    public function setPrimaryKeys(string $id, CustomerTypes $customerTypes, IdentifierTypes $identifierTypes)
    {
        $this->setId($id);
        $this->setCustomerTypes($customerTypes);
        $this->setIdentifierTypes($identifierTypes);
    }

    public function getcommercialName(): ?string
    {
        return $this->commercialName;
    }

    public function setCommercialName(?string $commercialName): self
    {
        $this->commercialName = $commercialName;

        return $this;
    } 

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function setMiddleName(?string $middleName): self
    {
        $this->middleName = $middleName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getSecondLastName(): ?string
    {
        return $this->secondLastName;
    }

    public function setSecondLastName(?string $secondLastName): self
    {
        $this->secondLastName = $secondLastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

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

    public function getCustomerTypes(): ?CustomerTypes
    {
        return $this->customerTypes;
    }

    public function setCustomerTypes(?CustomerTypes $customerTypes): self
    {
        $this->customerTypes = $customerTypes;

        return $this;
    }

    public function getIdentifierTypes(): ?IdentifierTypes
    {
        return $this->identifierTypes;
    }

    public function setIdentifierTypes(?IdentifierTypes $identifierTypes): self
    {
        $this->identifierTypes = $identifierTypes;

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