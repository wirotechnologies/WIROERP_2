<?php
namespace App\Controller;
use App\Repository\StatusRepository;
use App\Repository\CustomersRepository;
use App\Repository\CustomersPhonesRepository;
use App\Repository\CustomersAddressesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMapping;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
class RetrieveBasicCustomersBetweenMicroservicesController extends AbstractController
{
    public function __construct(
        private StatusRepository $statusRepository,
        private CustomersRepository $customersRepository,
        private CustomersPhonesRepository $customerPhoneRepository,
        private CustomersAddressesRepository $customerAddressRepository,
        private EntityManagerInterface $entityManager,
        )
    {}

    public function retrieveBasicCustomersBetweenMicroservices(SerializerInterface $serializer, Request $request, ManagerRegistry $doctrine, LoggerInterface $logger) : Response
    {
        $this->logger = $logger;
        $this->logger->info("ENTRO");
        $entityManager = $doctrine->getManager();
        $json = $request->getContent();
        //$json = '{"customersIds":[{"customersId":4616813,"customersCustomerTypesId":1,"customersIdentifierTypesId":1},{"customersId":6189038,"customersCustomerTypesId":1,"customersIdentifierTypesId":1},{"customersId":6220036,"customersCustomerTypesId":1,"customersIdentifierTypesId":1}]}';
        $conn = $entityManager->getConnection();
        // $query = "WITH json_data AS (SELECT :json::jsonb AS data)
        // SELECT c.id, c.commercial_name, c.customer_types_id, c.identifier_types_id, c.first_name, c.middle_name, c.last_name, c.second_last_name, c.email,c.created_date, c.updated_date
        // FROM json_data, jsonb_array_elements(data->'customersIds') ids(id), customers c
        // WHERE ids.id->>'customersId' = c.id";
        // $rsm = new ResultSetMappingBuilder($entityManager);
        // $rsm->addRootEntityFromClassMetadata('\App\Entity\Customers', 'c');
        //$rsm->addRootEntityFromClassMetadata('\App\Entity\CustomersAddresses', 'ca');
        //$rsm->addJoinedEntityFromClassMetadata('\App\Entity\CustomersAddresses', 'ca','c', 'customers');
        //$rsm->addFieldResult('ca', 'id', 'customerAddressId');
        //$rsm->addFieldResult('c', 'id', 'customer_id');

        //dd($rsm);
        //$selectClause = $rsm->generateSelectClause();
        //dd($query);
        // $rsm->addJoinedEntityFromClassMetadata('\App\Entity\CustomersAddresses', 'ca', 'c', 'customers_addresses', array('c.id' => 'ca.customers_id'));
        // $rsm = new ResultSetMappingBuilder($entityManager);
        // $rsm->addRootEntityFromClassMetadata('\App\Entity\Customers', 'c');
        // $rsm->addJoinedEntityFromClassMetadata('\App\Entity\CustomersAddresses', 'ca', 'c', 'customers_addresses', array('c.id' => 'ca.customers_id'));
       // $rsm->addFieldResult('c', 'customer_id', 'id');
        // $customerStatement = $entityManager->createNativeQuery($query, $rsm)
        //           ->setParameter('json', $json)
        //           ->getResult();
        // sort($customerStatement);
        // ca.id as customers_address_id, ca.line1, ca.socioeconomic_status, ca.cities_id,
        $query = "WITH json_data AS (SELECT :json::jsonb AS data)
        SELECT  c.id as customer_id, c.customer_types_id, c.identifier_types_id, c.first_name,
        c.middle_name, c.last_name, c.second_last_name, c.commercial_name, c.email,
        c.created_date, c.updated_date,
        ca.id as customers_address_id, ca.customers_customer_types_id as customer_types_id, ca.customers_identifier_types_id as identifier_types_id,
        ca.customers_id as customers_id,
        ca.line1, ca.socioeconomic_status, ca.cities_id, ca.status_id
        FROM json_data, jsonb_array_elements(data->'customersIds') ids(id), customers c
        INNER JOIN customers_addresses ca ON c.id = ca.customers_id
        WHERE ids.id->>'customersId' = c.id";
        $rsm = new ResultSetMapping();
        $rsm->addEntityResult('\App\Entity\Customers', 'c');
        $rsm->addFieldResult('c', 'customer_id', 'id');
        $rsm->addFieldResult('c', 'created_date', 'createdDate');
        $rsm->addFieldResult('c', 'updated_date', 'updatedDate');
        $rsm->addMetaResult('c', 'customer_types_id', 'customer_types_id');
        $rsm->addMetaResult('c', 'identifier_types_id', 'identifier_types_id');

        $rsm->addEntityResult('\App\Entity\CustomersAddresses', 'ca');
        $rsm->addFieldResult('ca', 'customers_address_id', 'id');
        $rsm->addFieldResult('ca', 'line1', 'line1');
        $rsm->addFieldResult('ca', 'socioeconomic_status', 'socioeconomicStatus');
        $rsm->addMetaResult('ca', 'customers_id', 'customers_id');
        $rsm->addMetaResult('ca', 'customers_customer_types_id', 'customers_customer_types_id');
        $rsm->addMetaResult('ca', 'customers_identifier_types_id', 'customers_identifier_types_id');
        $rsm->addMetaResult('ca', 'cities_id', 'cities_id');
        $rsm->addMetaResult('ca', 'status_id', 'status_id');
        // $rsm->addRootEntityFromClassMetadata('\App\Entity\CustomersAddresses', 'ca');
        // $rsm->addFieldResult('ca', 'customers_address_id', 'id');
        // $rsm->addMetaResult('ca', 'customer_types_id', 'customers');
        // $rsm->addMetaResult('ca', 'customers_id', 'customers');
        // $rsm->addMetaResult('ca', 'identifier_types_id', 'customers');
        $rsm->addJoinedEntityResult('\App\Entity\CustomersAddresses', 'ca', 'c', 'customersAddresses');
        $rsm->addFieldResult('ca', 'customers_address_id', 'id');
        $rsm->addMetaResult('ca', 'cities_id', 'cities_id');
        $rsm->addMetaResult('ca', 'status_id', 'status_id');
        // $rsm->addRootEntityFromClassMetadata('\App\Entity\CustomersAddresses', 'ca');
        // $rsm->addFieldResult('ca', 'customers_address_id', 'customers_address_id');
        // $rsm->addJoinedEntityResult('\App\Entity\CustomersAddresses', 'ca', 'c', 'customersAddresses');
        // $rsm->addMetaResult('ca', 'customers_id', 'customers_id');
        // $rsm->addMetaResult('ca', 'customers_customer_types_id', 'customers_customer_types_id');
        // $rsm->addMetaResult('ca', 'customers_identifier_types_id', 'customers_identifier_types_id');
        // $rsm->addMetaResult('ca', 'status_id', 'status_id');
        // $rsm->addMetaResult('ca', 'cities_id', 'cities_id');
        // $rsm->addFieldResult('ca', 'customers_address_id', 'id');
        // $rsm->addFieldResult('ca', 'line1', 'line1');
        // $rsm->addFieldResult('ca', 'socioeconomic_status', 'socioeconomicStatus');

        //$rsm->addJoinedEntityFromClassMetadata('\App\Entity\CustomersAddresses', 'ca', 'c', 'customers_addresses', array('ca.customers_id' => 'c.id'));
        // $rsm->addFieldResult('ca', 'customers_address_id', 'id');
        // $rsm->addMetaResult('ca', 'customers_id', 'customers_id');
        // $rsm->addMetaResult('ca', 'customers_customer_types_id', 'customers_customer_types_id');
        // $rsm->addMetaResult('ca', 'customers_identifier_types_id', 'customers_identifier_types_id');
        // $rsm->addMetaResult('ca', 'status_id', 'status_id');
        // $rsm->addMetaResult('ca', 'cities_id', 'cities_id');

        // $rsm->addEntityResult('\App\Entity\CustomersAddresses', 'ca');
        // $rsm->addFieldResult('ca', 'customers_address_id', 'id');
        // $rsm->addFieldResult('ca', 'line1', 'line1');
        // $rsm->addFieldResult('ca', 'socioeconomic_status', 'socioeconomicStatus');

        // $rsm->addEntityResult('\App\Entity\CustomersAddresses', 'ca');
        // $rsm->addFieldResult('ca', 'customers_address_id', 'id');
        // $rsm->addFieldResult('ca', 'line1', 'line1');
        // $rsm->addFieldResult('ca', 'socioeconomic_status', 'socioeconomicStatus');
        //$rsm->addFieldResult('ca', 'socioeconomic_status', 'socioeconomicStatus');
       // $rsm->addFieldResult('ca', 'line1', 'line1');
        // $rsm->addMetaResult('ca', 'customers_id', 'customers_id');
        // $rsm->addMetaResult('ca', 'customers_customer_types_id', 'customers_customer_types_id');
        // $rsm->addMetaResult('ca', 'customers_identifier_types_id', 'customers_identifier_types_id');
        // $rsm->addMetaResult('ca', 'status_id', 'status_id');
        // $rsm->addMetaResult('ca', 'cities_id', 'cities_id');
        // //$rsm->addRootEntityFromClassMetadata('\App\Entity\CustomersAddresses', 'ca');
        // $rsm->addJoinedEntityResult('\App\Entity\Customers', 'c', 'ca', 'customers');
        // $rsm->addFieldResult('c', 'customer_id', 'id');
        // $rsm->addMetaResult('c', 'customer_types_id', 'customer_types_id');
        // $rsm->addMetaResult('c', 'identifier_types_id', 'identifier_types_id');
        // $rsm->addFieldResult('c', 'first_name', 'firstName');
        // $rsm->addFieldResult('c', 'middle_name', 'middleName');
        // $rsm->addFieldResult('c', 'last_name', 'lastName');
        // $rsm->addFieldResult('c', 'second_last_name', 'secondLastName');
        // $rsm->addFieldResult('c', 'commercial_name', 'commercialName');
        // $rsm->addFieldResult('c', 'email', 'email');
        // $rsm->addFieldResult('c', 'created_date', 'createdDate');
        // $rsm->addFieldResult('c', 'updated_date', 'updatedDate');

        //$rsm->addFieldResult('c', 'customer_types_id', 'customerTypes');



        //$rsm->addFieldResult('c', 'id', 'id');
        //$rsm->addFieldResult('firstName', 'c.first_name', 'firstName');
        //$rsm->addMetaResult('ca', 'address_id', 'address_id');
        $customerStatement = $entityManager->createNativeQuery($query, $rsm)
        ->setParameter('json', $json)
        ->getResult();
        dd($customerStatement);
        sort($customerStatement);
        //dd($customerStatement);
        //dd($customerStatement);
        // $jsonResponse = [];
        // foreach($customerStatement as $customer){
        //    $jsonResponse[] = $customer->getBasicInfo();
        // }
        return $this->json([
            'customers' => $customerStatement

        ]);
        //dd($dataJson);

        $status = $this->statusRepository->find(1); //Status:Activo
        $jsonResponse = [];
        foreach($customersIds as $customerIds)
        {
            $customer = $this->customersRepository->findById($customerIds[0],  $customerIds[1],  $customerIds[2]);
            if(!$customer){
                $jsonResponse[] = [];
                continue;
            }
            $customerPhones = $this->customerPhoneRepository->findBy(['customers'=>$customer, 'status'=>$status]);
            $customerAddress = $this->customerAddressRepository->findOneBy(['customers'=>$customer, 'status'=>$status]);
            $jsonResponse[] = $customer->getBasicInfo($customerPhones, $customerAddress);
        }

        $response = new JsonResponse();
        $response->setContent(json_encode(['customers' => $jsonResponse]));
        return $response;
    }
}
