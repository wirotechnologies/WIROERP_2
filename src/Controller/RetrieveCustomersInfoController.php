<?php

namespace App\Controller;

use App\Service\RequestValidator\RequestValidator;
use App\Repository\CustomersRepository;
use App\Repository\TaxesInformationRepository;
use App\Repository\CustomersContactRepository;
use App\Repository\CustomersPhonesRepository;
use App\Repository\CustomersAddressesRepository;
use App\Repository\CustomersReferencesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;


class RetrieveCustomersInfoController extends AbstractController
{
    public function __construct(
        private RequestValidator $requestValidatorService,
        private CustomersRepository $customersRepository,
        private TaxesInformationRepository $taxesInformationRepository,
        private CustomersContactRepository $customerContactRepository,
        private CustomersPhonesRepository $customerPhoneRepository,
        private CustomersAddressesRepository $customerAddressRepository,
        private CustomersReferencesRepository $customerReferencesRepository,
        private EntityManagerInterface $entityManager,
        )
    {}

    public function retrieveCustomersInfo(Request $request, ManagerRegistry $doctrine, LoggerInterface $logger, HttpClientInterface $client) : Response
    {
        $this->logger = $logger;
        $this->logger->info("ENTRO");
        $entityManager = $doctrine->getManager();
        $dataJson = json_decode($request->getContent(), true);
        $customersIds = $dataJson['customersIds'];
        
        $jsonResponse = [];
        foreach($customersIds as $customerIds)
        {
            $identificationValue = $customerIds['customerIdentification'];
            $customerTypeId = $customerIds['customerType'];
            $identificationTypeId = $customerIds['customerIdentifierType'];
            $customer = $this->customersRepository->findById($identificationValue,  $customerTypeId,  $identificationTypeId);
            if(!$customer){
                array_push($jsonResponse,[
                    'id'=> $identificationValue,
                    'customerTypes'=> $customerTypeId,
                    'identifierTypes'=> $identificationTypeId,
                    'message'=> 'El cliente no existe'
                ]);
                continue;
            }
            $customerTaxesInformation = $this->taxesInformationRepository->findBy(['customers' => $customer]);
            $customerContacts = $this->customerContactRepository->findByCustomer($customer);
            $customerPhones = $this->customerPhoneRepository->findByCustomer($customer);
            $customerAddress = $this->customerAddressRepository->findOneByCustomer($customer);
            $customerReferences = $customerReferences = $this->customerReferencesRepository->findByCustomer($customer);
            array_push($jsonResponse,$customer->getAll($customerPhones, $customerAddress, $customerReferences, $customerContacts, $customerTaxesInformation));
        }

        $response = new JsonResponse();
        $response->setContent(json_encode(['customers' => $jsonResponse]));
        return $response;
    }
}