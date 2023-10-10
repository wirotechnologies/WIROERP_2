<?php

namespace App\Controller;

use App\Repository\CustomersRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class GetCustomerByIdsController extends AbstractController
{
    public function __construct(
        private CustomersRepository $customersRepository,
        )
    {}

    public function getCustomerByIds(SerializerInterface $serializer,LoggerInterface $logger, int $customerTypeId, int $identifierTypeId, int $customerId) : Response
    {
        $this->logger = $logger;
        $this->logger->info("ENTRO");
        $customer = $this->customersRepository->findBy(['id'=>$customerId, 'customerTypes'=>$customerTypeId, 'identifierTypes'=>$identifierTypeId]);
        if(!$customer){
            return $this->json(['message'=>'No se encontró el cliente'],404);
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