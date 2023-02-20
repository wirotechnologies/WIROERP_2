<?php
namespace App\Entity;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: CustomersRepository::class)]

class Customers
{
    #[Assert\Length(min: 4,max: 12)]
    #[Assert\NotBlank]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\Column(name:"id", type:"string", nullable:false)]
    private ?string $id;

    #[Assert\Type(CustomerTypes::class)]
    #[Assert\NotBlank]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"CustomerTypes")]
    #[ORM\JoinColumn(name:"customer_types_id", referencedColumnName:"id")]
    private $customerTypes;

    #[Assert\Type(IdentifierTypes::class)]
    #[Assert\NotBlank]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\ManyToOne(targetEntity:"IdentifierTypes")]
    #[ORM\JoinColumn(name:"identifier_types_id", referencedColumnName:"id")]
    private  $identifierTypes;

    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\OneToMany(targetEntity:"CustomersAddresses",mappedBy:"customers")]
    #[ORM\JoinColumn(name:"customers_addresses_id", referencedColumnName:"id")]
    private \Doctrine\Common\Collections\Collection $customersAddresses;

    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\OneToMany(targetEntity:"CustomersPhones",mappedBy:"customers")]
    #[ORM\JoinColumn(name:"customers_phones_id", referencedColumnName:"id")]
    private \Doctrine\Common\Collections\Collection $customersPhones;

    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\OneToMany(targetEntity:"CustomersContact",mappedBy:"customers")]
    #[ORM\JoinColumn(name:"customers_contacts_id", referencedColumnName:"id")]
    private \Doctrine\Common\Collections\Collection $customersContacts;

    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\OneToMany(targetEntity:"TaxesInformation",mappedBy:"customers")]
    #[ORM\JoinColumn(name:"taxes_information_id", referencedColumnName:"id")]
    private \Doctrine\Common\Collections\Collection $taxesInformation;

    #[ORM\GeneratedValue(strategy:"NONE")]
    #[ORM\OneToMany(targetEntity:"CustomersReferences",mappedBy:"customers")]
    #[ORM\JoinColumn(name:"customers_references_id", referencedColumnName:"id")]
    private \Doctrine\Common\Collections\Collection $customersReferences;

    #[Assert\Length(min: 3,max: 50)]
    #[Assert\Type('string')]
    #[ORM\Column(type: "string", length: 128, nullable: true)]
    private ?string $commercialName = null;

    #[Assert\Length(min: 3,max: 50)]
    #[Assert\Type('string')]
    #[ORM\Column(length: 128, nullable: true)]
    private ?string $firstName = null;
    
    #[Assert\Type('string')]
    #[ORM\Column(length: 128, nullable: true)]
    private ?string $middleName = null;

    #[Assert\Length(min: 3,max: 50)]
    #[Assert\Type('string')]
    #[ORM\Column(length: 128, nullable: true)]
    private ?string $lastName = null;

    #[Assert\Type('string')]
    #[ORM\Column(length: 128, nullable: true)]
    private ?string $secondLastName = null;

    #[Assert\Email]
    #[ORM\Column(length: 128, nullable: true)]
    private ?string $email = null;


    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdDate = null;

    public function getBasicInfoAndAddress(){
        return [
            'id'=> $this->id,
            'customerTypes'=> $this->customerTypes->getId(),
            'identifierTypes'=> $this->identifierTypes->getId(),
            'commercialName'=> $this->commercialName,
            'firstName'=>$this->firstName,
            'middleName'=>$this->middleName,
            'lastName'=>$this->lastName,
            'secondLastName'=>$this->secondLastName,
            'email'=>$this->email,
            'customersAddresses'=>$this->customersAddresses[0]
        ];
    }
    public function getAll($customerPhones, $customerAddress, $customerReferences, $customerContacts, $customerTaxesInformation)
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

        if(!$customerTaxesInformation){
            $taxesInformationArray = Null;
        }
        else{
            $taxesInformationArray = [
                'dvNit' => $customerTaxesInformation[0]->getDvNit(),
                'typePerson' => $customerTaxesInformation[0]->getTypePerson(),
                'granContribuyente' => $customerTaxesInformation[0]->isGranContribuyente(),
                'autorretenedor' => $customerTaxesInformation[0]->isAutorretenedor(),
                'agenteDeRetencionIVA' => $customerTaxesInformation[0]->isAgenteDeRetencionIVA(),
                'regimenSimple' => $customerTaxesInformation[0]->isRegimenSimple(),
                'impuestoNacionalConsumo' => $customerTaxesInformation[0]->isImpuestoNacionalConsumo(),
                'impuestoSobreVentasIVA' => $customerTaxesInformation[0]->isImpuestoSobreVentasIVA()
            ];

        }
        
        
        
        $phoneNumberArray = [];
        foreach($customerPhones as $customerPhone){
            array_push($phoneNumberArray, $customerPhone->getPhonesNumber()->getPhoneNumber());
        }

        $referencesArray = [];
        foreach($customerReferences as $customerReference){
            $contentReference = ['fullName'=>$customerReference->getFullName(),'typeReference'=> $customerReference->getTypeReference(), 'contactPhone'=>$customerReference->getPhoneNumber()];
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
            'taxesInformation'=>$taxesInformationArray,
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

    public function getCustomerTypes()//: //?CustomerTypes
    {
        return $this->customerTypes->getId();
    }

    public function setCustomerTypes(?CustomerTypes $customerTypes): self
    {
        $this->customerTypes = $customerTypes;

        return $this;
    }

    public function getIdentifierTypes()//: ?IdentifierTypes
    {
        return $this->identifierTypes->getId();
    }

    public function setIdentifierTypes(?IdentifierTypes $identifierTypes): self
    {
        $this->identifierTypes = $identifierTypes;

        return $this;
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


    public function getCreatedDate()//: ?\DateTimeInterface
    {
        return $this->createdDate->format('Y-m-d H:i:s');
    }

    public function setCreatedDate(?\DateTimeInterface $createdDate): self
    {
        $this->createdDate = $createdDate;

        return $this;
    }

   

    /**
     * Get the value of updatedDate
     */ 
    public function getUpdatedDate()
    {
        return $this->updatedDate->format('Y-m-d H:i:s');
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


    /**
     * Get the value of customersAddresses
     */ 
    public function getCustomersAddresses()
    {
        return $this->customersAddresses;
    }

    /**
     * Set the value of customersAddresses
     *
     * @return  self
     */ 
    public function setCustomersAddresses($customersAddresses)
    {
        $this->customersAddresses = $customersAddresses;

        return $this;
    }

    /**
     * Get the value of customersPhones
     */ 
    public function getCustomersPhones()
    {
        return $this->customersPhones;
    }

    /**
     * Set the value of customersPhones
     *
     * @return  self
     */ 
    public function setCustomersPhones($customersPhones)
    {
        $this->customersPhones = $customersPhones;

        return $this;
    }

    /**
     * Get the value of customersContacts
     */ 
    public function getCustomersContacts()
    {
        return $this->customersContacts;
    }

    /**
     * Set the value of customersContacts
     *
     * @return  self
     */ 
    public function setCustomersContacts($customersContacts)
    {
        $this->customersContacts = $customersContacts;

        return $this;
    }

    /**
     * Get the value of taxesInformation
     */ 
    public function getTaxesInformation()
    {
        return $this->taxesInformation;
    }

    /**
     * Set the value of taxesInformation
     *
     * @return  self
     */ 
    public function setTaxesInformation($taxesInformation)
    {
        $this->taxesInformation = $taxesInformation;

        return $this;
    }
}