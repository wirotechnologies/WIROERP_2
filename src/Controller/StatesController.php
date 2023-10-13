<?php

namespace App\Controller;
use App\Repository\StatesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
class StatesController extends AbstractController
{
    public function __construct(
       // private CountriesRepository $countryRepository,
        private StatesRepository $stateRepository
    )
    {
    }

    public function getStatesByCountryId(SerializerInterface $serializer, int $countryId)
    {
        //$country = $this->countryRepository->find($countryId);
        $states = $this->stateRepository->findBy(['countries'=>$countryId]);
        $states = $serializer->normalize($states, 'json', [AbstractNormalizer::ATTRIBUTES => ['id','name']]);
        return $this->json(['states'=>$states]);

    }

}