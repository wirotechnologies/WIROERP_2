<?php

namespace App\Controller;

use App\Repository\CustomersRepository;
use App\Service\RequestValidator\RequestValidator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class UpdateCustomerByIdController extends AbstractController
{


    public function __construct(
        private RequestValidator $requestValidator,
        private LoggerInterface $logger,
        private CustomersRepository $customersRepository
    )
    {
    }

    public function updateCustomerById(Request $request,int $customerTypeId,int $identifierTypeId,int $customerId)
    {
        $customer = $this->findOneBy([
            '$customerTypes'=>$customerTypeId,
            '$identifierTypes'=>$identifierTypeId,
            'id'=>$customerId
        ]);
        if(!$customer){
            return $this->json(['message'=>'No se encontró el cliente'],404);
        }
        $dataJson = json_decode($request->getContent(), true);
        if(!$dataJson){
            return $this->json('Nothing to do',200);
        }
        $badRequestValidator = $this->requestValidator->validateUpdateCustomer($dataJson);
        if($badRequestValidator){
            return $this->json($badRequestValidator,$badRequestValidator->getStatusCode());
        }
    }
}