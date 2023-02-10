<?php

namespace App\Controller;

use App\Service\RequestValidator\RequestValidator;
use App\Repository\CustomersRepository;
use App\Repository\CustomersPhonesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class retrieveCustomersController extends AbstractController
{
    public function __construct(
        private RequestValidator $requestValidatorService,
        private CustomersRepository $customersRepository,
        private CustomersPhonesRepository $customerPhoneRepository,
        private EntityManagerInterface $entityManager,
        )
    {}

    public function retrieveListCustomers(Request $request,  LoggerInterface $logger, HttpClientInterface $client) : Response
    {
        $this->logger = $logger;
        $this->logger->info("ENTRO");
        $requestValidator = $this->requestValidatorService->validateRequestRetrieveCustomers($request); 
        $customers = $this->customersRepository->findCustomers($request);
        if(!$customers){
            $response = new JsonResponse();
            $response->setStatusCode(404);
            $response->setContent('Cliente no encontrado');
            return $response;
        }
        $rows = $request->query->get('rows');
        $initialRow = $request->query->get('initialRow');
        $initialRow = $initialRow-1;
        //$rows = $rows - 1;
        $jsonResponse = [];
        
        foreach($customers as $customer){
            $customerPhones = $this->customerPhoneRepository->findByCustomer($customer);
            $customerContracts = $client->request('POST','http://api-dev.wiroerp.com:8099/v1/customer/retrieveContractInfo', ['body' => json_encode(['customerType' => $customer->getCustomerTypes()->getId(), 'identification' => ['value' => $customer->getId(), 'idIdentifierType'=>$customer->getIdentifierTypes()->getId()]])]);
            if($customerContracts->toArray()['CustomerContracts']){
                $customerContracts = $customerContracts->toArray()['CustomerContracts'];
                foreach($customerContracts as $customerContract){
                    array_push($jsonResponse,  $customer->getAllByRetrieve($customerPhones, $customerContract));
                }
            }
            else{
                $customerContract = ['IdContract' => 'No se encontraron contratos', 'Service_Id' => 'No contract found', 'Price' => 'No contract found', 'Balance' => 'No se encontraron contratos', 'Status' => 'No se encontraron contratos'];
                array_push($jsonResponse,  $customer->getAllByRetrieve($customerPhones, $customerContract));
            }
        }
        $totalResults = count($jsonResponse);
        $response = new JsonResponse();
        $response->setContent(json_encode(['customers' => array_slice($jsonResponse, $initialRow, $rows), 'initialResult' => $initialRow + 1 , 'lastResult' => $initialRow + 1 + $rows, 'totalResults' => $totalResults]));
        return $response;
    }
}
