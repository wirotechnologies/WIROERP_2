<?php

namespace App\Controller;

use App\Repository\CustomersRepository;
use App\Repository\StatusRepository;
use App\Repository\TaxesInformationRepository;
use App\Repository\TaxesTypePersonRepository;
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
use App\Service\RequestValidator\RequestValidator;
use App\Service\Serializer\Normalizer;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
class UpdateCustomerByIdController extends AbstractController
{
    public function __construct(
        private RequestValidator $requestValidator,
        private LoggerInterface $logger,
        private CustomersRepository $customersRepository,
        private StatusRepository $statusRepository,
        private CustomersAddressesRepository $customerAddressRepository,
        private CitiesRepository $cityRepository,
        private CustomersPhonesRepository $customerPhonesRepository,
        private PhonesNumbersRepository $phoneRepository,
        private CustomersPhonesRepository $customerPhoneRepository,
        private CountriesPhoneCodeRepository $countryPhoneCodeRepository,
        private CustomersReferencesRepository $customerReferenceRepository,
        private IdentifierTypesRepository $identifierTypeRepository,
        private ContactsRepository $contactRepository,
        private CustomersContactRepository $customerContactRepository,
        private TaxesInformationRepository $taxesInformationRepository,
        private TaxesTypePersonRepository $taxesTypePersonRepository,
        private CustomersFilesRepository $customerFileRepository,
        private Normalizer $normalizer,
    )
    {
    }

    public function updateCustomerById(Request $request,ManagerRegistry $doctrine,SerializerInterface $serializer,int $customerTypeId,int $identifierTypeId,int $customerId)
    {
        $customer = $this->customersRepository->findOneBy(['id'=>$customerId, 'customerTypes'=>$customerTypeId, 'identifierTypes'=>$identifierTypeId]);
        if(!$customer){
            return $this->json(['message'=>'No se encontró el cliente'],404);
        }
        
        $badRequestValidator = $this->requestValidator->validateUpdateCustomer($request);
        if($badRequestValidator){
            return $this->json($badRequestValidator,$badRequestValidator->getStatusCode());
        }
        $entityManager = $doctrine->getManager();
        $requestData = $request->get('request') ?? null;
        if($requestData){
            $customerAddress = null;
            $dataJson = json_decode($requestData, true);
            $customer = $this->customersRepository->update($customer, $dataJson);
            $entityManager->persist($customer);
            $activeStatus = $this->statusRepository->find(1); 
            $inactiveStatus = $this->statusRepository->find(2);
            $address = $dataJson['address'] ?? null;
            if($address){
                $currentCustomerAddress = $this->customerAddressRepository->findOneBy(['customers'=>$customer,'status'=>$activeStatus]);
                if($currentCustomerAddress){
                    $currentCustomerAddress = $this->customerAddressRepository->updateStatus($currentCustomerAddress,$inactiveStatus);
                    $entityManager->persist($currentCustomerAddress);
                }
                $cityId = $address['cityId'];
                $city = $this->cityRepository->find($cityId);
                $customerAddress = $this->customerAddressRepository->createCustomerAddress($address,$customer,$city,$activeStatus);
                $entityManager->persist($customerAddress);
            }
    
            $phoneNumbers = $dataJson['phoneNumbers'] ?? null;
            if($phoneNumbers){
                
                $country = $customerAddress ? $customerAddress->getCities()->getStates()->getCountries() :
                    $this->customerAddressRepository->findOneBy(['customers'=>$customer,'status'=>$activeStatus])->getCities()->getStates()->getCountries();
                $countryPhoneCode = $this->countryPhoneCodeRepository->findOneBy(['countries'=>$country]);

                $customersPhones = $this->customerPhonesRepository->findBy(['customers'=>$customer,'status'=>$activeStatus]);
                $currentPhoneNumbers = $this->normalizer->normalizeByField($customersPhones,'phonesNumber');
                $currentPhoneNumbers = $this->normalizer->normalizeByField($currentPhoneNumbers,'phoneNumber');
                $deletePhoneNumbers = array_diff($currentPhoneNumbers,$phoneNumbers);
                $createPhoneNumbers = array_diff($phoneNumbers,$currentPhoneNumbers);
                
                foreach($deletePhoneNumbers as $deletePhoneNumber){
                    $deletePhone = $this->phoneRepository->findOneBy(['phoneNumber'=>$deletePhoneNumber,'countriesPhoneCode'=>$countryPhoneCode]);
                    $deleteCustomerPhone = $this->customerPhonesRepository->findOneBy(['customers'=>$customer,'phonesNumber'=>$deletePhone]);
                    $deleteCustomerPhone = $this->customerPhonesRepository->updateStatus($deleteCustomerPhone,$inactiveStatus);
                    $entityManager->persist($deleteCustomerPhone);
                }
                foreach($createPhoneNumbers as $createPhoneNumber){
                    $createPhone = $this->phoneRepository->create($createPhoneNumber,$countryPhoneCode);
                    $entityManager->persist($createPhone);
                    $createCustomerPhone = $this->customerPhonesRepository->create($createPhone,$customer,$activeStatus);
                    $entityManager->persist($createCustomerPhone);
                }
            }
            
            $references = $dataJson['references'] ?? null;
            if($references){
                $country = $customerAddress ? $customerAddress->getCities()->getStates()->getCountries() :
                    $this->customerAddressRepository->findOneBy(['customers'=>$customer,'status'=>$activeStatus])->getCities()->getStates()->getCountries();
                $countryPhoneCode = $this->countryPhoneCodeRepository->findOneBy(['countries'=>$country]);

                foreach($references as $reference){
                    $typeReference = $reference['typeReference'];
                    $currentCustomerReference = $this->customerReferenceRepository->findOneBy(['customers'=>$customer,'typeReference'=>$typeReference,'status'=>$activeStatus]);
                    if($currentCustomerReference){
                        $currentCustomerReference = $this->customerReferenceRepository->updateStatus($currentCustomerReference,$inactiveStatus);
                        $entityManager->persist($currentCustomerReference);
                    }
                    $customerReference = $this->customerReferenceRepository->createCustomerReference($reference,$customer,$countryPhoneCode,$activeStatus);
                    $entityManager->persist($customerReference);
                }
            }
            if($customer->getCustomerTypes()->getId()==2){
                $mainContact = $dataJson['mainContact'] ?? null;
                if($mainContact){
                    $currentCustomerMainContact = $this->customerContactRepository->findOneBy(['customers'=>$customer,'status'=>$activeStatus]);
                    if($currentCustomerMainContact){
                        $currentCustomerMainContact = $this->customerContactRepository->updateStatus($currentCustomerMainContact,$inactiveStatus);
                        $entityManager->persist($currentCustomerMainContact);
                    }
                    $identifierTypeId = $mainContact['identifierTypeId'];
                    $contactIdentifierType = $this->identifierTypeRepository->find($identifierTypeId);
                    $contact = $this->contactRepository->createContact($mainContact,$contactIdentifierType);
                    $entityManager->persist($contact);
                    $customerMainContact = $this->customerContactRepository->createCustomerContact($contact,$customer,$activeStatus);
                    $entityManager->persist($customerMainContact);
                }
                $taxesInformation = $dataJson['taxesInformation'] ?? null;
                if($taxesInformation){
                    $taxTypePersonId = $taxesInformation['taxesTypePersonId'];
                    $taxTypePerson = $this->taxesTypePersonRepository->find($taxTypePersonId);
                    $customerTaxesInformation = $this->taxesInformationRepository->findOneBy(['customers'=>$customer]);
                    $customerTaxesInformation = $customerTaxesInformation ? $this->customerContactRepository->updateTaxesInformation($taxesInformation,$customerTaxesInformation,$taxTypePerson) :
                         $this->customerContactRepository->createTaxesInformation($taxesInformation,$taxTypePerson,$customer);
                    $entityManager->persist($customerTaxesInformation);
                }
            }
        }
        $files = $request->files->all() ?? null;
        if($files){
            $destination = $this->getParameter('customers_uploads');
            $fileEnergyInvoice = $request->files->get('fileEnergyInvoice') ?? null;
            if($fileEnergyInvoice){
               $currentFileEnergyInvoice = $this->customerFileRepository->findOneBy(['documentationType'=>'Factura de Energía','customers'=>$customer,'status'=>$activeStatus]);
               if($currentFileEnergyInvoice){
                    $currentFileEnergyInvoice = $this->customerFileRepository->updateStatus($currentFileEnergyInvoice,$inactiveStatus);
                    $entityManager->persist($currentFileEnergyInvoice);
                }
                $newFilenameEnergyInvoice = $this->uploadFilesService->upload($fileEnergyInvoice,$destination);
                $customerFileEnergyInvoice = $this->customerFilesRepository->create($newFilenameEnergyInvoice,$customer,$status);
                $customerFileEnergyInvoice->setDocumentationType('Factura de Energía');
                $entityManager->persist($customerFileEnergyInvoice); 
            }
            $identificationDocument = $request->files->get('identificationDocument') ?? null;
            if($identificationDocument){
                $currentDocumentIdentification = $this->customerFileRepository->findOneBy(['documentationType'=>'Documento de Identificación','customers'=>$customer,'status'=>$activeStatus]);
                if($currentDocumentIdentification){
                        $currentDocumentIdentification = $this->customerFileRepository->updateStatus($currentDocumentIdentification,$inactiveStatus);
                        $entityManager->persist($currentDocumentIdentification);
                }
                $newFilenameDocumentIdentification = $this->uploadFilesService->upload($identificationDocument,$destination);
                $customerFileDocumentIdentification = $this->customerFilesRepository->create($newFilenameDocumentIdentification,$customer,$status);
                $customerFileDocumentIdentification->setDocumentationType('Documento de Identificación');
                $entityManager->persist($customerFileDocumentIdentification); 
            }
            if($customer->getCustomerType()->getId()==2){
                $fileCamaraComercio = $request->files->get('fileCamaraComercio') ?? null;
                if($fileCamaraComercio){
                    $currentFileCamaraComercio = $this->customerFileRepository->findOneBy(['documentationType'=>'Cámara de Comercio','customers'=>$customer,'status'=>$activeStatus]);
                    if($currentFileCamaraComercio){
                            $currentFileCamaraComercio = $this->customerFileRepository->updateStatus($currentFileCamaraComercio,$inactiveStatus);
                            $entityManager->persist($currentFileCamaraComercio);
                    }
                    $newFilenameCamaraComercio = $this->uploadFilesService->upload($fileCamaraComercio,$destination);
                    $customerFileCamaraComercio = $this->customerFilesRepository->create($newFilenameCamaraComercio,$customer,$status);
                    $customerFileCamaraComercio->setDocumentationType('Cámara de Comercio');
                    $entityManager->persist($customerFileCamaraComercio); 
                }
                $fileRUT = $request->files->get('fileRUT') ?? null;
                if($fileRUT){
                    $currentFileRUT = $this->customerFileRepository->findOneBy(['documentationType'=>'RUT','customers'=>$customer,'status'=>$activeStatus]);
                    if($currentFileRUT){
                            $currentFileRUT = $this->customerFileRepository->updateStatus($currentFileRUT,$inactiveStatus);
                            $entityManager->persist($currentFileRUT);
                    }
                    $newFilenameRUT = $this->uploadFilesService->upload($fileRUT,$destination);
                    $customerFileRUT = $this->customerFilesRepository->create($newFilenameRUT,$customer,$status);
                    $customerFileRUT->setDocumentationType('RUT');
                    $entityManager->persist($customerFileRUT); 
                }
            }
        }
        $entityManager->flush();
        $customer = $serializer->normalize(
            $customer,
            'json',
            [AbstractNormalizer::ATTRIBUTES=>[
                'id',
                'customerTypes'=>['id','description'],
                'identifierTypes'=>['id','identifierName'],
                'customersAddresses'=>['id','cities'=>['id','name','states'=>['id','name','countries'=>['id','name']]],'status'=>['id','status'],'line1','line2','zipcode','socioeconomicStatus','note'],
                'customersPhones'=>['id','phonesNumber'=>['phoneNumber','countriesPhoneCode'=>['id','code','countries'=>['id','name']]],'status'=>['id','status']],
                'customersContacts'=>['id','contacts','status'=>['id','status']],
                'taxesInformation'=>['id','taxesTypePerson'=>['id','typePerson'],'dvNit','granContribuyente','autorretenedor','agenteDeRetencionIVA','regimenSimple','impuestoNacionalConsumo','impuestoSobreVentasIVA'],
                'customersReferences'=>['id','fullName','typeReference','phoneNumber'],
                'customersFiles'=>['id','status'=>['id','status'],'fileName','documentationType'],
                'commercialName',
                'firstName',
                'middleName',
                'lastName',
                'secondLastName',
                'email'
            ]]
        );
        return $this->json(['customer'=>$customer]);
    }
}