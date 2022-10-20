<?php
namespace App\Controller;

use App\Service\RequestValidator\RequestValidator;
use App\Repository\CustomersRepository;
use App\Repository\ContactsRepository;
use App\Repository\CustomersContactRepository;
use App\Repository\CustomerTypesRepository;
use App\Repository\IdentifierTypesRepository;
use App\Repository\CustomersAddressesRepository;
use App\Repository\PhonesNumbersRepository;
use App\Repository\CustomersPhonesRepository;
use App\Repository\CustomersReferencesRepository;
use App\Repository\CountriesRepository;
use App\Repository\CountriesPhoneCodeRepository;
use App\Repository\CitiesRepository;
use App\Repository\StatesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CustomersController extends AbstractController
{
    public function __construct(
        private RequestValidator $requestValidatorService,
        private CustomersRepository $customersRepository,
        private ContactsRepository $contactRepository,
        private CustomersContactRepository $customerContactRepository,
        private CustomersAddressesRepository $customerAddressRepository,
        private CustomerTypesRepository $customerTRepository,
        private IdentifierTypesRepository $identifierRepository,
        private CountriesRepository $countryRepository,
        private CountriesPhoneCodeRepository $countryPhoneRepository,
        private CitiesRepository $cityRepository,
        private StatesRepository $stateRepository,
        private PhonesNumbersRepository $phoneRepository,
        private CustomersPhonesRepository $customerPhoneRepository,
        private CustomersReferencesRepository $customerReferencesRepository,
        private EntityManagerInterface $entityManager
        )
        {}
    
    public function create(Request $request, ManagerRegistry $doctrine, LoggerInterface $logger) : Response
    {
        $this->logger = $logger;
        $this->logger->info("ENTRO");
        $entityManager = $doctrine->getManager();
        $dataJson = json_decode($request->getContent(), true);
        $requestValidator = $this->requestValidatorService->validateRequestCreateCustomer($dataJson);
        $customerId = $dataJson['identification']["value"];
        $customerType = $dataJson['customerType'];
        $customerIdentifierType = $dataJson['identification']['idIdentifierType'];
        
        $customer = $this->customersRepository->findById($customerId,$customerType,$customerIdentifierType);
        
        if(!is_null($customer)){
            throw new BadRequestHttpException('400 Customer already exist', null, 400);
        }

        $customer = $this->customersRepository->create($customerId, $customerType, $customerIdentifierType, $dataJson);
        $entityManager->persist($customer);

        if($customerType == 2){
            $mainContact = $dataJson['mainContact'];
            $contactId = $mainContact['identification']['value'];
            $identTypeContact = $mainContact['identification']['idIdentifierType'];
            $contact = $this->contactRepository->findById($contactId,$identTypeContact);
            if(is_null($contact)){
                $contact = $this->contactRepository->create($dataJson);
                $entityManager->persist($contact);
            }
            $customerContact = $this->customerContactRepository->create($customer, $contact);
            $entityManager->persist($customerContact);  
        } 

        $customerAddress = $this->customerAddressRepository->create($dataJson, $customer);
        $entityManager->persist($customerAddress);
    
        $phoneNumbers = $dataJson['phoneNumbers'];
        $nameCountry = $dataJson['address']['country'];
        $country = $this->countryRepository-> findByName($nameCountry);
        $countryPhoneCode = $this->countryPhoneRepository->findOneByCountry($country);

        foreach ($phoneNumbers as $phoneNumber){
            $number = $this->phoneRepository->findById($phoneNumber, $countryPhoneCode);
            if(is_null($number)){
                $number = $this->phoneRepository->create($phoneNumber, $countryPhoneCode);
                $entityManager->persist($number);
            }
            $customerPhone = $this->customerPhoneRepository->create($number, $customer);
            $entityManager->persist($customerPhone);
        }
            
        $references = $dataJson['references'];
        foreach($references as $reference){
            $customerReference = $this->customerReferencesRepository->create($reference, $customer, $countryPhoneCode);
            $entityManager->persist($customerReference);
        }

        $entityManager->flush(); 
        $idCustomer = $customer->getId();
        $response = new JsonResponse();
        $response->setStatusCode(201); 
        $response->setContent(json_encode(['Created_Customer' => $idCustomer])) ;
        return $response;
        

    }


    public function update(Request $request, ManagerRegistry $doctrine, LoggerInterface $logger) : Response
    {
        $this->logger = $logger;
        $this->logger->info("ENTRO");
        $entityManager = $doctrine->getManager();
        $dataJson = json_decode($request->getContent(), true);
        $requestValidator = $this->requestValidatorService->validateRequestUpdateCustomer($dataJson);
        $customerId=  $dataJson['identification']["value"];
        $customerType=  $dataJson['customerType'];
        $customerIdentifierType =  $dataJson['identification']['idIdentifierType'];
        
        $customer = $this->customersRepository->findById($customerId,$customerType,$customerIdentifierType);
        
        if(is_null($customer)){
            throw new BadRequestHttpException('400 Customer not exist', null, 400);
        }

        $customer = $this->customersRepository->update($customer, $dataJson);
        $entityManager->persist($customer);
        $mainContact = isset($dataJson['mainContact']) ? $dataJson['mainContact']:Null;
        if($customerType == 2 and !is_null($mainContact)){
            $contactId = $mainContact['identification']['value'];
            $identTypeContact = $mainContact['identification']['idIdentifierType'];
            $contact = $this->contactRepository->findById($contactId, $identTypeContact);
            if($contact == Null){
                $contact = $this->contactRepository->create($dataJson);
                $entityManager->persist($contact);
                $customerContact = $this->customerContactRepository->create($customer, $contact);
                $entityManager->persist($customerContact);  
            }
            else{
                $contact = $this->contactRepository->update($dataJson, $contact);
                $entityManager->persist($contact);
            } 
        }

        $address =isset($dataJson['address']) ? $dataJson['address']:Null;
        if(!is_null($address)){
            $customerAddress = $this->customerAddressRepository->findOneByCustomer($customer);
            $customerAddress = $this->customerAddressRepository->update($dataJson, $customerAddress);
            $entityManager->persist($customerAddress);  
        } 

        $phoneNumbers = $dataJson['phoneNumbers'] ? $dataJson['phoneNumbers']:Null;
        $customerAddressForCountry = $this->customerAddressRepository->findOneByCustomer($customer);
        $nameCountry = $customerAddressForCountry->getCities()->getStates()->getCountries()->getName();
        $country = $this->countryRepository->findByName($nameCountry);
        $countryPhoneCode = $this->countryPhoneRepository->findOneByCountry($country);
        
        if(!is_null($phoneNumbers)){
            foreach ($phoneNumbers as $phoneNumber){
                $number = $this->phoneRepository->findById($phoneNumber, $countryPhoneCode);
                
                if(is_null($number)){
                    $number = $this->phoneRepository->create($phoneNumber, $countryPhoneCode);
                    $entityManager->persist($number);
                    $newCustomerPhone = $this->customerPhoneRepository->create($number, $customer);
                    $entityManager->persist($newCustomerPhone);
                    continue;
                }
                else{
                    $newCustomerPhone = Null;
                    
                    $customerPhones =  $this->customerPhoneRepository->findByCustomer($customer);
                    foreach($customerPhones as $customerPhone){
                        if($customerPhone->getPhonesNumber()==$number){
                            $newCustomerPhone = $customerPhone;
                            break;
                        }
                    }
                    if($newCustomerPhone == Null){
                        $newCustomerPhone = $this->customerPhoneRepository->create($number, $customer);
                        $entityManager->persist($newCustomerPhone);
                    }
                }
            }
        }    
  
        $references = $dataJson['references'] ? $dataJson['references']:Null;
        $newCustomerReference = Null;
        
        if(!is_null($references)){
            $customerReferences = $this->customerReferencesRepository->findByCustomer($customer);
            foreach($references as $reference){
                foreach($customerReferences as $customerReference){
                    if(($customerReference->getFullName()==$reference['fullName']) and ($customerReference->getPhoneNumber()==$reference['contactPhone'])){
                        $newCustomerReference = $customerReference;
                        break 1;
                    }
                }   
            dd($reference);     
            if($newCustomerReference == Null){
               dd("hola"); 
                $newCustomerReference = $this->customerReferencesRepository->create($reference, $customer, $countryPhoneCode);
                $entityManager->persist($newCustomerReference);
            }  
            }
            dd("2 break");
            
        }

        $entityManager->flush();  
        $idCustomer = $customer->getId();
        $response = new JsonResponse();
        $response->setContent(json_encode(['Updated_Customer' => $idCustomer])) ;
        return $response;
    }
}      
    

