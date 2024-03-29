<?php
namespace App\Controller;
use App\Entity\Customers;
use App\Entity\CustomersAddresses;
use App\Entity\PhonesNumbers;
use App\Entity\CustomersPhones;
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
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use Psr\Log\LoggerInterface;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CreateCustomersByCSVController extends AbstractController
{
    public function __construct(
        private CustomersRepository $customersRepository,
        private StatusRepository $statusRepository,
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
        private EntityManagerInterface $entityManager
        )
        {}
    
    public function createBasicResidentialCustomersByCSV(Request $request, ManagerRegistry $doctrine, LoggerInterface $logger) : Response
    {
        $this->logger = $logger;
        $this->logger->info("ENTRO");
        $entityManager = $doctrine->getManager();
        //$csvFile = $request->files->get('csvFile');   
        $csvFile = '/app/var/storage/customers_comercial_Febrero3.csv';
        $csvOpen = fopen($csvFile, "r");
        $row = 1;
        while(($data = fgetcsv($csvOpen,10000, ',')) !== false){ //fgetcvs returns false if ends csv
            // if( $row == 1 or $data[0] == '901155978' or  $data[0] == '9007282963' or  $data[0] == '890324611' or  $data[0] ==  '900590431' or  $data[0] == '9011137248' or  $data[0] == '805019862' or  $data[0] == '16449325'  or  $data[0] == '31998191' or  $data[0] ==  '1144089221' ){
            //     $row++;
            //     continue;
            // }
            if($row == 1){
                $row++;
                continue;
            }
            
        

            // $data = ^ array:12 [
            //     0 => "government_id"
            //     1 => "name"
            //     2 => "middle_name"
            //     3 => "lastname"
            //     4 => "second_surname"
            //     5 => "id_products"
            //     6 => "email"
            //     7 => "stratum"
            //     8 => "contract"
            //     9 => "created_date"
            //     10 => "line_1"
            //     11 => "number"
            //   ]
            $createdDate = $data[9] != "NULL" ? new \DateTime($data[9]): new \DateTime();
            
            $identifierType = $this->identifierRepository->find(2);
            $customerType = $this->customerTRepository->find(2);
            $customer = $this->customersRepository->findOneBy(['id'=>$data[0], 'customerTypes'=>$customerType, 'identifierTypes'=>$identifierType]);
            if(!$customer){
                $governmentId = $data[0];
                $firstName = $data[1];
                $middleName = $data[2] != "NULL" ? $data[2] : Null;
                $lastName = $data[3];
                $secondLastName = $data[4] != "NULL" ? $data[4] : Null;
                $email = $data[6] != "NULL" ? $data[6] : Null;
                $commercialName = $firstName;
                if($middleName){
                    $commercialName = $commercialName.' '.$middleName;
                }
                if($lastName){
                    $commercialName = $commercialName.' '.$lastName;
                }
                if($secondLastName){
                    $commercialName = $commercialName.' '.$secondLastName;
                }
                $customer = new Customers();
                $customer->setPrimaryKeys($governmentId, $customerType, $identifierType);
                $customer->setCommercialName($commercialName);
                // $customer->setFirstName($firstName);
                // $customer->setMiddleName($middleName);
                // $customer->setLastName($lastName);
                // $customer->setSecondLastName($secondLastName);
                $customer->setCreatedDate($createdDate);
                $customer->setUpdatedDate($createdDate);
                $customer->setEmail($email);
                $entityManager->persist($customer);  
                $entityManager->flush();  
            }

            $status = $this->statusRepository->find(1); //Active = 1

            $customerAddress = $this->customerAddressRepository->findOneBy(['customers'=>$customer, 'status'=>$status]);
            if(!$customerAddress){
                $socioeconomicStatus = $data[7];
                $line1 = $data[10];
                $customerAddress = new CustomersAddresses;
                $customerAddress->setCustomers($customer);
                $customerAddress->setLine1($line1);
                $customerAddress->setSocioeconomicStatus($socioeconomicStatus);
                $customerAddress->setStatus($status);
                $customerAddress->setCreatedDate($createdDate);
                $entityManager->persist($customerAddress);   
                $entityManager->flush(); 
            }

            $number = $data[11];
            $countryPhoneCode = $this->countryPhoneRepository->find(1); //1 Colombia
            $phoneNumber = $this->phoneRepository->findOneBy(['phoneNumber'=>$number, 'countriesPhoneCode'=>$countryPhoneCode]);
            if(!$phoneNumber){
                $phoneNumber = new PhonesNumbers();
                $phoneNumber->setPrimaryKeys($number,$countryPhoneCode);
                $entityManager->persist($phoneNumber);
                $entityManager->flush(); 
            }

            $customerPhone = $this->customerPhoneRepository->findOneBy(['phonesNumber'=>$phoneNumber, 'customers'=>$customer]);
            if(!$customerPhone){
                $customerPhone = new CustomersPhones();
                $customerPhone->setPhonesNumber($phoneNumber);
                $customerPhone->setCustomers($customer);
                $customerPhone->setCreatedDate($createdDate);
                $customerPhone->setStatus($status);
                $entityManager->persist($customerPhone);
                $entityManager->flush(); 
            }
            $row++;
        }

        fclose($csvOpen);
        //$entityManager->flush(); 
        $response = new JsonResponse();
        $response->setStatusCode(201); 
        $response->setContent(json_encode(['quantityCreatedCustomers' => $row])) ;
        return $response;
    }

    public function createCVSMarch(Request $request, ManagerRegistry $doctrine, LoggerInterface $logger) : Response
    {
        $this->logger = $logger;
        $this->logger->info("ENTRO");
        $entityManager = $doctrine->getManager();
        //$csvFile = $request->files->get('csvFile');   
        $csvFile = '/app/var/storage/CLIENTES POR CREAR EN WIRO- Hoja1.csv';
        $csvOpen = fopen($csvFile, "r");
        $row = 1;

        while(($data = fgetcsv($csvOpen,10000, ',')) !== false){ //fgetcvs returns false if ends csv
            
            if( $row == 1 or $data[0] == '24870867' or  $data[0] == '1112493699' or  $data[0] == '31446960' or $data[0] == '29119157'){
                $row++;
                continue;
            }
            //dd(new \DateTime($data[11]));
            // if($row == 1){
            //     $row++;
            //     continue;
            // }
            
        

            // $data =  array:16 [
                //   0 => "Cedula"
                //   1 => "Primer nombre"
                //   2 => "Segundo nombre"
                //   3 => "Primer apellido "
                //   4 => "Segundo apellido "
                //   5 => "id_products" //id servicio
                //   6 => "Id_products"
                //   7 => "Nodo"
                //   8 => "Email"
                //   9 => "Estrato"
                //   10 => "Contrato"
                //   11 => "Fecha inicio"
                //   12 => "Direccion"
                //   13 => "Urbanizacion "
                //   14 => "Numero"
                //   15 => ""
                // ]
            $createdDate = new \DateTime();
            
            $identifierType = $this->identifierRepository->find(1);
            $customerType = $this->customerTRepository->find(1);
            $customer = $this->customersRepository->findOneBy(['id'=>$data[0], 'customerTypes'=>$customerType, 'identifierTypes'=>$identifierType]);
            if(!$customer){
                $governmentId = $data[0];
                $firstName = $data[1];
                $middleName = $data[2] != "" ? $data[2] : Null;
                $lastName = $data[3];
                $secondLastName = $data[4] != "" ? $data[4] : Null;
                $email = $data[8] != "" ? $data[8] : Null;
                
                $customer = new Customers();
                $customer->setPrimaryKeys($governmentId, $customerType, $identifierType);
                $customer->setFirstName($firstName);
                $customer->setMiddleName($middleName);
                $customer->setLastName($lastName);
                $customer->setSecondLastName($secondLastName);

                $customer->setCreatedDate($createdDate);
                $customer->setUpdatedDate($createdDate);
                $customer->setEmail($email);
                $entityManager->persist($customer);  
                $entityManager->flush();  

                $status = $this->statusRepository->find(1); //Active = 1
                $city = $this->cityRepository->find(1); //Jamundí
                $customerAddress = $this->customerAddressRepository->findOneBy(['customers'=>$customer, 'status'=>$status]);
                if(!$customerAddress){
                    $socioeconomicStatus = $data[9];
                    $line1 = $data[13] != "" ? $data[12].''.$data[13] : $data[12];
                    $customerAddress = new CustomersAddresses;
                    $customerAddress->setCustomers($customer);
                    $customerAddress->setLine1($line1);
                    $customerAddress->setSocioeconomicStatus($socioeconomicStatus);
                    $customerAddress->setStatus($status);
                    $customerAddress->setCities($city);
                    $customerAddress->setCreatedDate($createdDate);
                    $entityManager->persist($customerAddress);   
                    $entityManager->flush(); 
                }

                $number = $data[14] != "" ?  $data[14]: null; 
                if($number){
                    $countryPhoneCode = $this->countryPhoneRepository->find(1); //1 Colombia
                    $phoneNumber = $this->phoneRepository->findOneBy(['phoneNumber'=>$number, 'countriesPhoneCode'=>$countryPhoneCode]);
                    if(!$phoneNumber){
                        $phoneNumber = new PhonesNumbers();
                        $phoneNumber->setPrimaryKeys($number,$countryPhoneCode);
                        $entityManager->persist($phoneNumber);
                        $entityManager->flush(); 
                    }
        
                    $customerPhone = $this->customerPhoneRepository->findOneBy(['phonesNumber'=>$phoneNumber, 'customers'=>$customer]);
                    if(!$customerPhone){
                        $customerPhone = new CustomersPhones();
                        $customerPhone->setPhonesNumber($phoneNumber);
                        $customerPhone->setCustomers($customer);
                        $customerPhone->setCreatedDate($createdDate);
                        $customerPhone->setStatus($status);
                        $entityManager->persist($customerPhone);
                        $entityManager->flush(); 
                    }
                }
                
            }
            $row++;
        }

        fclose($csvOpen);
        $entityManager->flush(); 
        $response = new JsonResponse();
        $response->setStatusCode(201); 
        $response->setContent(json_encode(['quantityCreatedCustomers' => $row])) ;
        return $response;
    }

    public function createCsvAppointments(Request $request, ManagerRegistry $doctrine, LoggerInterface $logger) : Response
    {
        $this->logger = $logger;
        $this->logger->info("ENTRO");
        $entityManager = $doctrine->getManager();
        //$csvFile = $request->files->get('csvFile');   
        $csvFile = '/app/var/storage/1-20.csv';
        $csvOpen = fopen($csvFile, "r");
        $row = 1;
        $quantityCreatedCustomers = 0;
        while(($data = fgetcsv($csvOpen,10000, ',')) !== false){ //fgetcvs returns false if ends csv

            // $data = ^ array:10 [
            //     0 => "name"
            //     1 => "middle_name"
            //     2 => "last_name"
            //     3 => "second_last_name"
            //     4 => "address"
            //     5 => "phone"
            //     6 => "email"
            //     7 => "docid"
            //     8 => "stratum"
            //     9 => "customer_type"
            //   ]


            if( $row == 1){
                $row++;
                continue;
            }
            if($row == 13){
                break;
            }
            
    
            
            $createdDate = new \DateTime();
            
            $identifierType = $this->identifierRepository->find(1);
            $customerType = $this->customerTRepository->find(1);
            $customer = $this->customersRepository->findBy(['id'=>$data[7]]);

            if(!$customer){
                
                $governmentId = $data[7];
                $firstName = $data[0];
                $middleName = $data[1] != "" ? $data[1] : Null;
                $lastName = $data[2];
                $secondLastName = $data[3] != "" ? $data[3] : Null;
                $email = $data[6] != "" ? $data[6] : Null;
                
                $customer = new Customers();
                $customer->setPrimaryKeys($governmentId, $customerType, $identifierType);
                $customer->setFirstName($firstName);
                $customer->setMiddleName($middleName);
                $customer->setLastName($lastName);
                $customer->setSecondLastName($secondLastName);

                $customer->setCreatedDate($createdDate);
                $customer->setUpdatedDate($createdDate);
                $customer->setEmail($email);
                $entityManager->persist($customer);  
                $entityManager->flush();  

                $status = $this->statusRepository->find(1); //Active = 1
                $city = $this->cityRepository->find(1); //Jamundí
                $customerAddress = $this->customerAddressRepository->findOneBy(['customers'=>$customer, 'status'=>$status]);
                if(!$customerAddress){
                    $socioeconomicStatus = $data[8];
                    $line1 = $data[4];
                    $customerAddress = new CustomersAddresses;
                    $customerAddress->setCustomers($customer);
                    $customerAddress->setLine1($line1);
                    $customerAddress->setSocioeconomicStatus($socioeconomicStatus);
                    $customerAddress->setStatus($status);
                    $customerAddress->setCities($city);
                    $customerAddress->setCreatedDate($createdDate);
                    $entityManager->persist($customerAddress);   
                    $entityManager->flush(); 
                }

                $number = $data[5]; 
                if($number){
                    $countryPhoneCode = $this->countryPhoneRepository->find(1); //1 Colombia
                    $phoneNumber = $this->phoneRepository->findOneBy(['phoneNumber'=>$number, 'countriesPhoneCode'=>$countryPhoneCode]);
                    if(!$phoneNumber){
                        $phoneNumber = new PhonesNumbers();
                        $phoneNumber->setPrimaryKeys($number,$countryPhoneCode);
                        $entityManager->persist($phoneNumber);
                        $entityManager->flush(); 
                    }
        
                    $customerPhone = $this->customerPhoneRepository->findOneBy(['phonesNumber'=>$phoneNumber, 'customers'=>$customer]);
                    if(!$customerPhone){
                        $customerPhone = new CustomersPhones();
                        $customerPhone->setPhonesNumber($phoneNumber);
                        $customerPhone->setCustomers($customer);
                        $customerPhone->setCreatedDate($createdDate);
                        $customerPhone->setStatus($status);
                        $entityManager->persist($customerPhone);
                        $entityManager->flush(); 
                    }
                }
                $quantityCreatedCustomers++;
            }
            
            $row++;
        }

        fclose($csvOpen);
        $entityManager->flush(); 
        $response = new JsonResponse();
        $response->setStatusCode(201); 
        $response->setContent(json_encode(['quantityCreatedCustomers' => $quantityCreatedCustomers, 'rows'=>$row])) ;
        return $response;
    }

}