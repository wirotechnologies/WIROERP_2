<?php

namespace App\Controller;

use App\Service\RequestValidator\RequestValidator;
use App\Repository\CustomersRepository;
use App\Repository\CustomersContactRepository;
use App\Repository\CustomersPhonesRepository;
use App\Repository\CustomersAddressesRepository;
use App\Repository\CustomersReferencesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class GetCustomerByIdsController extends AbstractController
{
    public function __construct(
        private RequestValidator $requestValidatorService,
        private CustomersRepository $customersRepository,
        private CustomersContactRepository $customerContactRepository,
        private CustomersPhonesRepository $customerPhoneRepository,
        private CustomersAddressesRepository $customerAddressRepository,
        private CustomersReferencesRepository $customerReferencesRepository,
        private EntityManagerInterface $entityManager,
        )
    {}

    public function getCustomerByIds(Request $request, ManagerRegistry $doctrine, LoggerInterface $logger, int $customerTypeId, int $identificationTypeId, int $identificationvalue) : Response
    {
        $this->logger = $logger;
        $this->logger->info("ENTRO");
        $entityManager = $doctrine->getManager();

        $customer = $this->customersRepository->findBy(['id'=>$identificationvalue, 'customerTypes'=>$customerTypeId, 'identifierTypes'=>$identificationTypeId]);
        if(!$customer){

        }
        return $this->json([
            'customer' => $customer[0]->getBasicInfoAndAddress()
        ]); 
        dd($customer);
        if($customerTypeId == 2){
            $query = "
        SELECT  c.id as customer_id, c.customer_types_id, c.identifier_types_id, c.first_name, c.middle_name, c.last_name, c.second_last_name, c.commercial_name, c.email, c.created_date, c.updated_date
        FROM  customers c
        INNER JOIN customers_addresses ca ON c.id = ca.customers_id
        INNER JOIN customers_phones cp ON c.id = cp.customers_id
        INNER JOIN customers_contact cc ON c.id = cc.customers_id
        WHERE c.id = :identificationvalue
        AND c.customer_types_id = :customerTypeId
        AND c.identifier_types_id = :identificationTypeId
        AND ca.status_id = 1
        AND cp.status_id = 1
        AND cc.status_id = 1";
        $rsm = new ResultSetMappingBuilder($entityManager);
        $rsm->addRootEntityFromClassMetadata('\App\Entity\Customers', 'c');
        $rsm->addFieldResult('c', 'customer_id', 'id');
        $rsm->addMetaResult('c', 'customer_types_id', 'customer_types_id');
        $rsm->addMetaResult('c', 'identifier_types_id', 'identifier_types_id');
        $customerStatement = $entityManager->createNativeQuery($query, $rsm)
        ->setParameter('identificationvalue', $identificationvalue)
        ->setParameter('customerTypeId', $customerTypeId)
        ->setParameter('identificationTypeId', $identificationTypeId)
        ->getResult();
        sort($customerStatement);
        return $this->json([
            'customer' => $customerStatement
            
        ]); 
        }

         
        else{
            $query = "
        SELECT  c.id as customer_id, c.customer_types_id, c.identifier_types_id, c.first_name, c.middle_name, c.last_name, c.second_last_name, c.commercial_name, c.email, c.created_date, c.updated_date
        FROM  customers c
        INNER JOIN customers_addresses ca ON c.id = ca.customers_id
        INNER JOIN customers_phones cp ON c.id = cp.customers_id
        WHERE c.id = :identificationvalue
        AND c.customer_types_id = :customerTypeId
        AND c.identifier_types_id = :identificationTypeId
        AND ca.status_id = 1
        AND cp.status_id = 1";
        $rsm = new ResultSetMappingBuilder($entityManager);
        $rsm->addRootEntityFromClassMetadata('\App\Entity\Customers', 'c');
        $rsm->addFieldResult('c', 'customer_id', 'id');
        $rsm->addMetaResult('c', 'customer_types_id', 'customer_types_id');
        $rsm->addMetaResult('c', 'identifier_types_id', 'identifier_types_id');
        $customerStatement = $entityManager->createNativeQuery($query, $rsm)
        ->setParameter('identificationvalue', $identificationvalue)
        ->setParameter('customerTypeId', $customerTypeId)
        ->setParameter('identificationTypeId', $identificationTypeId)
        ->getResult();
        sort($customerStatement);
        return $this->json([
            'customer' => $customerStatement
            
        ]); 
        }


        $query = "
        SELECT  c.id as customer_id, c.customer_types_id, c.identifier_types_id, c.first_name, c.middle_name, c.last_name, c.second_last_name, c.commercial_name, c.email, c.created_date, c.updated_date, 
        ca.id as customers_address_id, ca.line1, ca.line2, ca.zipcode, ca.note, ca.socioeconomic_status, ca.cities_id, ca.status_id,
        cp.id as customers_phones_id, cp.phones_numbers_phone_number as phones_number, cp.phones_numbers_countries_phone_code_id as countries_phone_code_id, cp.status_id,
        cc.id as customers_contacts_id, cc.contacts_id, cc.contacts_identifier_types_id
        
        FROM  customers c
        INNER JOIN customers_addresses ca ON c.id = ca.customers_id
        INNER JOIN customers_phones cp ON c.id = cp.customers_id
        INNER JOIN customers_contact cc ON c.id = cc.customers_id
        WHERE c.id = :identificationvalue
        AND c.customer_types_id = :customerTypeId
        AND c.identifier_types_id = :identificationTypeId
        AND ca.status_id = 1
        AND cp.status_id = 1
        AND cc.status_id = 1";
        $rsm = new ResultSetMappingBuilder($entityManager);
        $rsm->addRootEntityFromClassMetadata('\App\Entity\Customers', 'c');
        $rsm->addFieldResult('c', 'customer_id', 'id');
        $rsm->addMetaResult('c', 'customer_types_id', 'customer_types_id');
        $rsm->addMetaResult('c', 'identifier_types_id', 'identifier_types_id');
        $customerStatement = $entityManager->createNativeQuery($query, $rsm)
        ->setParameter('identificationvalue', $identificationvalue)
        ->setParameter('customerTypeId', $customerTypeId)
        ->setParameter('identificationTypeId', $identificationTypeId)
        ->getResult();
        sort($customerStatement);
        return $this->json([
            'customer' => $customerStatement
            
        ]); 
        // $rsm->addJoinedEntityResult('\App\Entity\CustomersAddresses', 'ca', 'c', 'customersAddresses');
        // $rsm->addFieldResult('ca', 'customers_address_id', 'id');
        // $rsm->addFieldResult('ca', 'line1', 'line1');
        // $rsm->addFieldResult('ca', 'line2', 'line2');
        // $rsm->addFieldResult('ca', 'zipcode', 'zipcode');
        // $rsm->addMetaResult('ca', 'customers_id', 'customers_id');
        // $rsm->addMetaResult('ca', 'customers_customer_types_id', 'customers_customer_types_id');
        // $rsm->addMetaResult('ca', 'customers_identifier_types_id', 'customers_identifier_types_id');
        // $rsm->addMetaResult('ca', 'status_id', 'status_id');
        // $rsm->addMetaResult('ca', 'cities_id', 'cities_id');
        // $rsm->addJoinedEntityResult('\App\Entity\CustomersPhones', 'cp', 'c', 'customersPhones');
        // $rsm->addFieldResult('cp', 'customers_phones_id', 'id');
        // $rsm->addMetaResult('cp', 'customers_id', 'customers_id');
        // $rsm->addMetaResult('cp', 'customers_customer_types_id', 'customers_customer_types_id');
        // $rsm->addMetaResult('cp', 'customers_identifier_types_id', 'customers_identifier_types_id');
        // $rsm->addMetaResult('cp', 'phones_number', 'phone_number');
        // $rsm->addMetaResult('cp', 'countries_phone_code_id', 'countries_phone_code_id');
        // $rsm->addMetaResult('cp', 'status_id', 'status_id');
        $customerStatement = $entityManager->createNativeQuery($query, $rsm)
        ->setParameter('identificationvalue', $identificationvalue)
        ->setParameter('customerTypeId', $customerTypeId)
        ->setParameter('identificationTypeId', $identificationTypeId)
        ->getResult();
        sort($customerStatement);
        return $this->json([
            'customer' => $customerStatement
            
        ]); 


        $customer = $this->customersRepository->findById($identificationvalue,  $customerTypeId,  $identificationTypeId);
        if(!$customer){
            $response = new JsonResponse();
            $response->setContent(json_encode(['message'=>'Cliente no encontrado']));
            $response->setStatusCode(404);
            return $response;
        }
        $customerContacts = $this->customerContactRepository->findByCustomer($customer);
        $customerPhones = $this->customerPhoneRepository->findByCustomer($customer);
        $customerAddress = $this->customerAddressRepository->findOneByCustomer($customer);
        $customerReferences = $customerReferences = $this->customerReferencesRepository->findByCustomer($customer);
        $jsonResponse = [$customer->getAll($customerPhones, $customerAddress, $customerReferences, $customerContacts)];

        $response = new JsonResponse();
        $response->setContent(json_encode(['customer' => $jsonResponse]));
        return $response;
    }
}