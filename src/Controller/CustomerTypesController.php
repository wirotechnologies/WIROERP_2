<?php

namespace App\Controller;

use App\Repository\CustomerTypesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class CustomerTypesController extends AbstractController
{


    public function __construct(
        private CustomerTypesRepository $customerTypesRepository
    )
    {
    }

    public function getCustomerTypes(SerializerInterface $serializer)
    {
        $customerTypes = $this->customerTypesRepository->findAll();
        if(!$customerTypes){
            return $this->json([
                'message'=>'No se encontraton tipos de clientes'
            ],400);
        }
        $customerTypes = $serializer->normalize(
            $customerTypes,
            'json',
            [
                AbstractNormalizer::ATTRIBUTES=>[
                    'id',
                    'description'
                ]
            ]
        );
        return $this->json([
            'customerTypes'=>$customerTypes
        ]);
    }
}