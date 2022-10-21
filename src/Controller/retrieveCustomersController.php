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

class retrieveCustomersController extends AbstractController
{
    public function __construct(
        private RequestValidator $requestValidatorService,
        private CustomersRepository $customersRepository,
        private CustomersPhonesRepository $customerPhoneRepository,
        private EntityManagerInterface $entityManager,
        )
    {}

    public function retrieveListCustomers(Request $request,  LoggerInterface $logger) : Response
    {
        $this->logger = $logger;
        $this->logger->info("ENTRO");
        $requestValidator = $this->requestValidatorService->validateRequestRetrieveCustomers($request); 
        $customers = $this->customersRepository->findCustomers($request);

        $this->logger->info(json_last_error_msg());
        $rows = count($customers);
        $jsonResponse = [];
        $customerAddress = Null;
        foreach ($customers as  $customer) {
            $customerPhones = $this->customerPhoneRepository->findByCustomer($customer);
            array_push($jsonResponse,  $customer->getAllByRetrieve($customerPhones));
        }
        
        $response = new JsonResponse();
        $response->setContent(json_encode(['customers' => $jsonResponse, 'rows' => $rows]));
        return $response;
    }
}