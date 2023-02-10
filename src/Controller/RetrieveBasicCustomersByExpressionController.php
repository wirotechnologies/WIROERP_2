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
class RetrieveBasicCustomersByExpressionController extends AbstractController
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
        $expression = strtoupper($request->query->get('expression'));
        $status = $this->statusRepository->find(1); //Status:Activo
        $jsonResponse = [];
        $statementByExpression = "(upper(c.commercialName) LIKE "."'%".$expression."%'"." OR upper(c.firstName) LIKE "."'%".$expression."%'"." OR upper(c.middleName) LIKE "."'%".$expression."%'"." OR upper(c.lastName) LIKE "."'%".$expression."%'"." OR upper(c.secondLastName) LIKE "."'%".$expression."%'"." OR c.id LIKE "."'%".$expression."%')";
        $customersByExpression = $this->customersRepository->retrieveCustomersByExpression($statementByExpression);
        foreach($customersByExpression as $customer){
            $customerPhones = $this->customerPhoneRepository->findBy(['customers'=>$customer, 'status'=>$status]);
            $customerAddress = $this->customerAddressRepository->findOneBy(['customers'=>$customer, 'status'=>$status]);
            $jsonResponse[] = $customer->getBasicInfo($customerPhones, $customerAddress);
        }

        $response = new JsonResponse();
        $response->setContent(json_encode(['customers' => $jsonResponse]));
        return $response;
    }
}
