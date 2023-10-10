<?php

namespace App\Controller;

use App\Repository\TaxesTypePersonRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
class TaxesTypesPersonController extends AbstractController
{


    public function __construct(
        private TaxesTypePersonRepository $taxesTypePersonRepository
    )
    {
    }

    public function getTypesPerson(Request $request,SerializerInterface $serializer){
        $taxesTypePersonId = $request->get('taxesTypesPersonId');
        $initialRow = $request->get('initialRow');
        $rows = $request->get('rows');
        if($taxesTypePersonId){
            $taxesTypePersonId = explode(",",$taxesTypePersonId);
        }
        $taxesTypesPerson = $this->taxesTypePersonRepository->filter($taxesTypePersonId,$initialRow,$rows);
        if(!$taxesTypesPerson){
            return $this->json([
                'message'=>'No se encontraron tipos de personas'
            ],404);
        }
        $taxesTypesPerson = $serializer->normalize(
            $taxesTypesPerson,
            'json',
            [AbstractNormalizer::ATTRIBUTES => [
                'id',
                'typePerson'
            ]]
        );
        $response['taxesTypesPerson'] = $taxesTypesPerson;
        if($initialRow and $rows){
            $totalResults = $this->taxesTypePersonRepository->countResults(
                $taxesTypePersonId,
            );

            $endResults = min($totalResults, $initialRow+$rows-1);
            $response['beginResults'] = intval($initialRow);
            $response['endResults'] = $endResults;
            $response['totalResults'] = $totalResults;
        }

        return $this->json($response);
    }
}