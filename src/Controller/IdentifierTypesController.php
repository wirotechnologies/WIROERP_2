<?php

namespace App\Controller;

use App\Repository\IdentifierTypesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class IdentifierTypesController extends AbstractController
{


    public function __construct(
        private IdentifierTypesRepository $identifierTypesRepository
    )
    {
    }

    public function getIdentifierTypes(SerializerInterface $serializer)
    {
        $identifierTypes = $this->identifierTypesRepository->findAll();
        if(!$identifierTypes){
            return $this->json([
                'message'=>'No se encontraton tipos de clientes'
            ],400);
        }
        $identifierTypes = $serializer->normalize(
            $identifierTypes,
            'json',
            [
                AbstractNormalizer::ATTRIBUTES=>[
                    'id',
                    'identifierName',
                    'description'
                ]
            ]
        );
        return $this->json([
            'identifierTypes'=>$identifierTypes
        ]);
    }
}