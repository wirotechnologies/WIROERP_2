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

    public function getCustomerByIds(Request $request,  LoggerInterface $logger, int $customerTypeId, int $identificationTypeId, int $identificationvalue) : Response
    {
        $this->logger = $logger;
        $this->logger->info("ENTRO");
        $customer = $this->customersRepository->findById($identificationvalue,  $customerTypeId,  $identificationTypeId);
        if(!$customer){
            $response = new JsonResponse();
            $response->setContent('Cliente no encontrado');
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