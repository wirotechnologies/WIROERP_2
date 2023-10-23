<?php

namespace App\Controller;

use App\Repository\CustomersRepository;
use App\Service\RequestValidator\RequestValidator;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
class UpdateCustomerByIdController extends AbstractController
{
    public function __construct(
        private RequestValidator $requestValidator,
        private LoggerInterface $logger,
        private CustomersRepository $customersRepository
    )
    {
    }

    public function updateCustomerById(Request $request,ManagerRegistry $doctrine,SerializerInterface $serializer,int $customerTypeId,int $identifierTypeId,int $customerId)
    {
        $customer = $this->customersRepository->findOneBy(['id'=>$customerId, 'customerTypes'=>$customerTypeId, 'identifierTypes'=>$identifierTypeId]);
        if(!$customer){
            return $this->json(['message'=>'No se encontró el cliente'],404);
        }
        
        $badRequestValidator = $this->requestValidator->validateUpdateCustomer($request);
        if($badRequestValidator){
            return $this->json($badRequestValidator,$badRequestValidator->getStatusCode());
        }
        $entityManager = $doctrine->getManager();
        $requestData = $request->get('request') ?? null;
        if($requestData){
            $dataJson = json_decode($requestData, true) ?? null;
            $customer = $this->customersRepository->update($customer, $dataJson);
            $entityManager->persist($customer);
    
            $phoneNumbers = $dataJson['phoneNumbers'] ?? null;
            if($phoneNumbers){
    
            }
            $address = $dataJson['address'] ?? null;
            if($address){
    
            }
            $references = $dataJson['references'] ?? null;
            if($references){
    
            }
            if($customer->getCustomerType()->getId()==2){
                $mainContact = $dataJson['mainContact'] ?? null;
                if($mainContact){
    
                }
                $taxesInformation = $dataJson['taxesInformation'] ?? null;
                if($taxesInformation){
    
                }
            }
        }
        
        $customer = $serializer->normalize(
            $customer,
            'json',
            [AbstractNormalizer::ATTRIBUTES=>[
                'id',
                'customerTypes'=>['id','description'],
                'identifierTypes'=>['id','identifierName'],
                'customersAddresses'=>['id','cities'=>['id','name','states'=>['id','name','countries'=>['id','name']]],'status'=>['id','status'],'line1','line2','zipcode','socioeconomicStatus','note'],
                'customersPhones'=>['id','phonesNumber'=>['phoneNumber','countriesPhoneCode'=>['id','code','countries'=>['id','name']]],'status'=>['id','status']],
                'customersContacts'=>['id','contacts','status'=>['id','status']],
                'taxesInformation'=>['id','taxesTypePerson'=>['id','typePerson'],'dvNit','typePerson','granContribuyente','autorretenedor','agenteDeRetencionIVA','regimenSimple','impuestoNacionalConsumo','impuestoSobreVentasIVA'],
                'customersReferences'=>['id','fullName','typeReference','phoneNumber'],
                'customersFiles'=>['id','status'=>['id','status'],'fileName','documentationType'],
                'commercialName',
                'firstName',
                'middleName',
                'lastName',
                'secondLastName',
                'email'
            ]]
        );
        return $this->json(['customer'=>$customer]);
    }
}