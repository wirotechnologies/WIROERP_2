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


class ExrpressionRetrieveCustomersController extends AbstractController
{
    public function __construct(
        private RequestValidator $requestValidatorService,
        private CustomersRepository $customersRepository,
        private CustomersPhonesRepository $customerPhoneRepository,
        private EntityManagerInterface $entityManager,
        )
    {}

    public function retrieveCustomersByExpression(Request $request,  LoggerInterface $logger, HttpClientInterface $client) : Response
    {
        $this->logger = $logger;
        $this->logger->info("ENTRO");
        $requestValidator = $this->requestValidatorService->validateRequestRetrieveCustomersByExpression($request); 
        $expression = $request->query->get('expression');
        $customers = $this->customersRepository->findOnlyByExpresion($expression);
        if(!$customers){
            $response = new JsonResponse();
            $response->setContent(json_encode(['customers' => []]));
            return $response;
        }
        $retrieveCustomers = [];
        foreach($customers as $customer){
            $customerPhones = $this->customerPhoneRepository->findByCustomer($customer);
            array_push($retrieveCustomers, $customer->getByExpressionRetrieveCustomer($customerPhones));
        }
        $response = new JsonResponse();
        $response->setContent(json_encode(['customers' => $retrieveCustomers]));
        return $response;
    }
}