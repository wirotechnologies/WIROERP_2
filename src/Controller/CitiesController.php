<?php

namespace App\Controller;
use App\Repository\CitiesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
class CitiesController extends AbstractController
{
    public function __construct(
        //private StatesRepository $stateRepository,
        private CitiesRepository $cityRepository,
    )
    {
    }

    public function getCitiesByStateId(SerializerInterface $serializer, int $stateId)
    {

        $state = $this->cityRepository->find($stateId);
        $cities = $this->cityRepository->findBy(['states'=>$state]);
        $cities = $serializer->normalize($cities, 'json', [AbstractNormalizer::ATTRIBUTES => ['id','name']]);
        return $this->json(['cities'=>$cities]);

    }
}