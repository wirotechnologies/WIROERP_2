<?php
namespace App\Controller;

use App\Service\RequestValidator\RequestValidator;
use App\Repository\CustomersRepository;
use App\Repository\StatusRepository;
use App\Repository\TaxesInformationRepository;
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
use App\Repository\CustomersFilesRepository;
use App\Service\Files\UploadFiles;
//use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CustomersController extends AbstractController
{
    public function __construct(
        private RequestValidator $requestValidatorService,
        private CustomersRepository $customersRepository,
        private StatusRepository $statusRepository,
        private TaxesInformationRepository $taxesInformationRepository,
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
        private CustomersFilesRepository $customerFilesRepository,
        private UploadFiles $uploadFilesService,
        //private EntityManagerInterface $entityManager
        )
        {}
    
    public function create(Request $request, ManagerRegistry $doctrine, LoggerInterface $logger, ValidatorInterface $validator) : Response
    {
        $this->logger = $logger;
        $this->logger->info("Request POST CreateCustomer");
        $entityManager = $doctrine->getManager();
        //$dataJson = json_decode($request->get('request'), true);
        $dataJson = json_decode($request->getContent(), true);
        try{
            $customerId =  $dataJson['identification']["value"];
            $customerType =  $dataJson['customerType'];
            $customerIdentifierType =  $dataJson['identification']['idIdentifierType'];
            if(!$customerId or !$customerType or !$customerIdentifierType or !$this->identifierRepository->find($customerIdentifierType) or ! $this->customerTRepository->find($customerType)){
                $this->logger->warning("Error: Incomplete identification number");
                $response = new JsonResponse(['message' =>'Debe ingresar No. Documento, tipo de cliente y tipo de identificación válidos']);
                $response->setStatusCode(400);
                return $response;
            }
            $logger->info("Get customer by ids: customerTypes = {$customerType}, identifierTypes = {$customerIdentifierType}, id = {$customerId}");
            $customer = $this->customersRepository->findById($customerId, $customerType, $customerIdentifierType);
            
        }
        catch(\Exception $e)  {
            $logger->error($e->getMessage());
            return new JsonResponse(['message' => 'Error: Verifique que los campos requeridos sean válidos'], 400);
        }
        if($customer){
            $this->logger->info("Conflict: Customer already exist");
            $response = new JsonResponse();
            $response->setContent(json_encode(['message'=> 'El cliente ya existe']));
            $response->setStatusCode(409);
            return $response;
        }
        $requestValidator = $this->requestValidatorService->validateRequestCreateCustomer($dataJson, $request);
        $this->logger->info("Request validated successfully");

        $customer = $this->customersRepository->create($customerId, $customerType, $customerIdentifierType, $dataJson);
        $errors = $validator->validate($customer);
        if (count($errors) > 0) {
            return new JsonResponse(['message' => 'Error: Verifique que todos los campos requeridos sean suministrados y válidos'], 400);
        }
        $this->logger->info("validator customer pass");
        $entityManager->persist($customer);

       
        $status = $this->statusRepository->find(1); //Status:Activo for create phone, address, files, contacts, references
        if(!$status){
            return new JsonResponse(['message' => 'Error inesperado'], 500);
        }

        

        if($customerType == 2){
            //taxesInformation guarda la informacion de obligaciones tributarias del cliente comercial
            $taxesInformation = $this->taxesInformationRepository->create($customer, $dataJson);
            $errors = $validator->validate($taxesInformation);
            
            if (count($errors) > 0) {
                return new JsonResponse(['message' => 'Error: Verifique que todos los campos requeridos sean suministrados y válidos'], 400);
            }
            $this->logger->info("validator taxesInformation pass");
            $entityManager->persist($taxesInformation);
            
            $mainContact = $dataJson['mainContact'];
            $contactId = $mainContact['identification']['value'];
            $identTypeContact = $mainContact['identification']['idIdentifierType'];
            
            $contact = $this->contactRepository->findById($contactId,$identTypeContact);
           
            if(!$contact){
                $contact = $this->contactRepository->create($dataJson);
                $errors = $validator->validate($contact);
                
                if (count($errors) > 0) {
                    return new JsonResponse(['message' => 'Error:  Verifique que todos los campos requeridos sean suministrados y válidos'], 400);
                }
                $this->logger->info("validator contact pass");
                $entityManager->persist($contact);
            }
            $customerContact = $this->customerContactRepository->create($customer, $contact, $status);
            $errors = $validator->validate($customerContact);
            if (count($errors) > 0) {
                return new JsonResponse(['message' => 'Error: Verifique que todos los campos requeridos sean suministrados y válidos'], 400);
            }
            $this->logger->info("validator customerContact pass");
            $entityManager->persist($customerContact);  
        } 

        $customerAddress = $this->customerAddressRepository->create($dataJson, $customer, $status);
        $errors = $validator->validate($customerAddress);
        if (count($errors) > 0) {
            return new JsonResponse(['message' => 'Error: Verifique que todos los campos requeridos sean suministrados y válidos'], 400);
        }
        $this->logger->info("validator customerAddress pass");
        $entityManager->persist($customerAddress);
        
        try{
            $nameCountry = $dataJson['address']['country'];
            $country = $this->countryRepository->findOneBy(['name'=>$nameCountry]);
            $countryPhoneCode = $this->countryPhoneRepository->findOneBy(['countries'=>$country]);
            if(!$country or !$countryPhoneCode){
                return new JsonResponse(['message' => 'Error: Verifique que los campos requeridos sean válidos'], 400);
            }
        }
        catch(\Exception $e)  {
            $logger->error($e->getMessage());
            return new JsonResponse(['message' => 'Error: Verifique que los campos requeridos sean válidos'], 400);
        }
        $phoneNumbers = $dataJson['phoneNumbers'];
        foreach ($phoneNumbers as $phoneNumber){
            $number = $this->phoneRepository->findBy(['phoneNumber'=>$phoneNumber]);
            if(!$number){
                $number = $this->phoneRepository->create($phoneNumber, $countryPhoneCode);
                $errors = $validator->validate($number);
                if (count($errors) > 0) {
                    return new JsonResponse(['message' => 'Error: Verifique que todos los campos requeridos sean suministrados y válidos'], 400);
                }
                $this->logger->info("validator number pass");
                $entityManager->persist($number);
            }
            $customerPhone = $this->customerPhoneRepository->create($number,$customer,$status);
            $errors = $validator->validate($customerPhone);
            if (count($errors) > 0) {
                return new JsonResponse(['message' => 'Error: Verifique que todos los campos requeridos sean suministrados y válidos'], 400);
            }
            $this->logger->info("validator customerPhone pass");
            $entityManager->persist($customerPhone);
        }
            
        $references = $dataJson['references'];
        foreach($references as $reference){
            $customerReference = $this->customerReferencesRepository->create($reference,$customer,$countryPhoneCode,$status);
            $errors = $validator->validate($customerReference);
            if (count($errors) > 0) {
                return new JsonResponse(['message' => 'Error: Verifique que todos los campos requeridos sean suministrados y válidos'], 400);
            }
            $entityManager->persist($customerReference);
        }
        $this->logger->info("validator references pass");
        
        $destination = $this->getParameter('customers_uploads');
        
        $uploadFileEnergyInvoice = $request->files->get('fileEnergyInvoice');
        try{
            $newFilenameEnergyInvoice = $this->uploadFilesService->upload($uploadFileEnergyInvoice,$destination);
        }
        catch(\Exception $e)  {
            $logger->error($e->getMessage());
            return new JsonResponse(['message' => 'Error Inesperado'], 500);
        }
        $uploadedFileEnergyInvoice = $this->customerFilesRepository->create($newFilenameEnergyInvoice,$customer,$status);
        $uploadedFileEnergyInvoice->setDocumentationType('Factura de Energía');
        $entityManager->persist($uploadedFileEnergyInvoice); 

        $uploadFileIdentificationDocument = $request->files->get('identificationDocument');
        try{
            $newFilenameIdentificationDocument = $this->uploadFilesService->upload($uploadFileIdentificationDocument,$destination);
        }
        catch(\Exception $e)  {
            $logger->error($e->getMessage());
            return new JsonResponse(['message' => 'Error Inesperado'], 500);
        }
        $uploadedFileIdentificationDocument = $this->customerFilesRepository->create($newFilenameIdentificationDocument,$customer,$status);
        $uploadedFileIdentificationDocument->setDocumentationType('Documento de Identificación');
        $entityManager->persist($uploadedFileIdentificationDocument); 

        if($customerType == 2){
            $uploadFileCamaraComercio = $request->files->get('fileCamaraComercio');
            try{
                $newFilenameCamaraComercio = $this->uploadFilesService->upload($uploadFileCamaraComercio, $destination);
            }
            catch(\Exception $e)  {
                $logger->error($e->getMessage());
                return new JsonResponse(['message' => 'Error Inesperado'], 500);
            }
            $uploadedFileCamaraComercio = $this->customerFilesRepository->create($newFilenameCamaraComercio,$customer,$status);
            $uploadedFileCamaraComercio->setDocumentationType('Cámara de Comercio');
            $entityManager->persist($uploadedFileCamaraComercio);

            $uploadFileRUT = $request->files->get('fileRUT');
            try{
                $newFilenameRUT = $this->uploadFilesService->upload($uploadFileRUT, $destination);
            }
            catch(\Exception $e)  {
                $logger->error($e->getMessage());
                return new JsonResponse(['message' => 'Error Inesperado'], 500);
            }
            $uploadedFileRUT = $this->customerFilesRepository->create($newFilenameRUT,$customer,$status);
            $uploadedFileRUT->setDocumentationType('RUT');
            $entityManager->persist($uploadedFileRUT);
        }

        $entityManager->flush(); 
        $idCustomer = $customer->getId();
        $response = new JsonResponse();
        $response->setStatusCode(201); 
        $response->setContent(json_encode(['createdCustomer' => $idCustomer])) ;
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
        
        if(!$customer){
            $this->logger->error('Customer not exist');
            $response = new JsonResponse();
            $response->setContent(json_encode(['error'=> 'El cliente no existe']));
            //$response->setStatusCode(404);
            return $response;
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

        $phoneNumbers = isset($dataJson['phoneNumbers']) ? $dataJson['phoneNumbers']:Null;
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
        
        
        if(!is_null($references)){
            $customerReferences = $this->customerReferencesRepository->findByCustomer($customer);
            foreach($references as $reference){
                $newCustomerReference = Null;
                foreach($customerReferences as $customerReference){
                    if(($customerReference->getFullName()==$reference['fullName']) and ($customerReference->getPhoneNumber()==$reference['contactPhone'])){
                        $newCustomerReference = $customerReference;
                        continue 1;
                    }
                }     
            if($newCustomerReference == Null){
                $newCustomerReference = $this->customerReferencesRepository->create($reference, $customer, $countryPhoneCode);
                $entityManager->persist($newCustomerReference);
            }  
            }
        }

        $entityManager->flush();  
        $idCustomer = $customer->getId();
        $response = new JsonResponse();
        $response->setContent(json_encode(['updatedCustomer' => $idCustomer])) ;
        return $response;
    }
}      
    

