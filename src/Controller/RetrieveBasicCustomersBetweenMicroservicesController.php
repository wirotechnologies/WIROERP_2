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

    public function retrieveBasicCustomersBetweenMicroservices(Request $request, ManagerRegistry $doctrine, LoggerInterface $logger) : Response
    {
        $this->logger = $logger;
        $this->logger->info("ENTRO");
        $entityManager = $doctrine->getManager();
        $json = $request->getContent();
        $json = json_decode($json);
        //$json = '{"customersIds":[{"customersId":4616813,"customersCustomerTypesId":1,"customersIdentifierTypesId":1},{"customersId":6189038,"customersCustomerTypesId":1,"customersIdentifierTypesId":1},{"customersId":6220036,"customersCustomerTypesId":1,"customersIdentifierTypesId":1}]}';
        $conn = $entityManager->getConnection();
        $query = "WITH json_data AS (SELECT :json::jsonb AS data)
        SELECT *
        FROM json_data, jsonb_array_elements(data->'customersIds') ids(id), customers c
        WHERE ids.id->>'customersId' = c.id";

        //$stmt = $conn->prepare($query);
        //$stmt->bindValue('json', $json);
        $rsm = new ResultSetMappingBuilder($entityManager);
       // $rsm->addEntityResult('\App\Entity\CustomerForJson', 'c');
       // $rsm->addFieldResult('c', 'id', 'id');
        //$rsm->addJoinedEntityResult('CustomerTypes', 'ct','c')
        //$rsm->addFieldResult('c', 'customer_types_id', 'customer_types_id');
        //$rsm->addFieldResult('c', 'id', 'id');
        //$rsm->addFieldResult('c', 'first_name', 'firstName');
        $rsm->addRootEntityFromClassMetadata('\App\Entity\Customers', 'c');
        $stmt2 = $entityManager->createNativeQuery($query, $rsm)
                  ->setParameter('json', $json)
                  ->getResult();
        

        //dd($stmt2);
        //$stmt->execute();
        //$result = $stmt->getResult();
        return $this->json([
            'customers' => $stmt2
            
        ]); 
        dd($dataJson);
        //$customersIds = $dataJson['customersIds'];
        
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
            