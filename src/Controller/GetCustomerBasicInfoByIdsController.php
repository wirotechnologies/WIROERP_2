<?php

namespace App\Controller;

use App\Service\RequestValidator\RequestValidator;
use App\Repository\CustomersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class GetCustomerBasicInfoByIdsController extends AbstractController
{
    public function __construct(
        private RequestValidator $requestValidatorService,
        private CustomersRepository $customersRepository,
        private EntityManagerInterface $entityManager
        )
    {}

    public function getCustomerByIds(Request $request, ManagerRegistry $doctrine, LoggerInterface $logger, int $customerTypeId, int $identificationTypeId, int $identificationvalue) : Response
    {
        $this->logger = $logger;
        $this->logger->info("ENTRO");
        $entityManager = $doctrine->getManager();

        $customer = $this->customersRepository->findBy(['id'=>$identificationvalue, 'customerTypes'=>$customerTypeId, 'identifierTypes'=>$identificationTypeId]);
        if(!$customer){
            $response = new JsonResponse();
            $response->setContent(json_encode(['message'=>'Cliente no encontrado']));
            $response->setStatusCode(404);
            return $response;
        }

        return $this->json([
            'customer' => $customer[0]->getBasicInfoAndAddress()
        ]); 
    }
}