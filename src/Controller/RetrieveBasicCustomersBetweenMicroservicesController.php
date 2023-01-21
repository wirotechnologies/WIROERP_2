<?php
namespace App\Controller;
use App\Repository\StatusRepository;
use App\Repository\CustomersRepository;
use App\Repository\CustomersPhonesRepository;
use App\Repository\CustomersAddressesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;
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
        $dataJson = json_decode($request->getContent(), true);
        $customersIds = $dataJson['customersIds'];
        $status = $this->statusRepository->find(1); //Status:Activo
        $jsonResponse = [];
        foreach($customersIds as $customerIds)
        {
            if(count($customersIds)==1){
                $statementByIds = "(c.id = '".$customerIds[0]."' AND c.customerTypes = ".$customerIds[1]." AND c.identifierTypes = ".$customerIds[2].")";
            }
            else{
                foreach($customersIds as $key => $customerId){
                
                    if($key == 0){
                        $statementByIds = "((c.id = '".$customerId[0]."' AND c.customerTypes = ".$customerId[1]." AND c.identifierTypes = ".$customerId[2].")";
                    }
                    if($customerId == end($customersIds)){
                        $statementByIds = $statementByIds." OR (c.id = '".$customerId[0]."' AND c.customerTypes = ".$customerId[1]." AND c.identifierTypes = ".$customerId[2]."))";
                    }
                    else{
                        $statementByIds = $statementByIds." OR (c.id = '".$customerId[0]."' AND c.customerTypes = ".$customerId[1]." AND c.identifierTypes = ".$customerId[2].")";
                    }
    
                }
            }
        }
        $jsonResponse = [];
        $customers = $this->customersRepository->retrieveByIds($statementByIds);
        foreach($customers as $customer){
            $customerPhones = $this->customerPhoneRepository->findBy(['customers'=>$customer, 'status'=>$status]);
            $customerAddress = $this->customerAddressRepository->findOneBy(['customers'=>$customer, 'status'=>$status]);
            $jsonResponse[] = $customer->getBasicInfo($customerPhones, $customerAddress);
        }
        
        $response = new JsonResponse();
        $response->setContent(json_encode(['customers' => $jsonResponse]));
        return $response;
    }
}
            // $identificationValue = $customerIds['customerIdentification'];
            // $customerTypeId = $customerIds['customerType'];
            // $identificationTypeId = $customerIds['customerIdentifierType'];
            // $customer = $this->customersRepository->findById($identificationValue,  $customerTypeId,  $identificationTypeId);
            // if(!$customer){
            //     $jsonResponse[] = [];
            //     continue;
            // }
            // $customerPhones = $this->customerPhoneRepository->findBy(['customers'=>$customer, 'status'=>$status]);
            // $customerAddress = $this->customerAddressRepository->findOneBy(['customers'=>$customer, 'status'=>$status]);
            // $jsonResponse[] = $customer->getBasicInfo($customerPhones, $customerAddress);