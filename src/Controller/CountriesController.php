<?php

namespace App\Controller;
use App\Repository\CountriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class CountriesController extends AbstractController
{
    public function __construct(
        private CountriesRepository $countryRepository
    )
    {
    }

    public function getCountries(SerializerInterface $serializer)
    {
        $countries = $this->countryRepository->findAll();
        $countries = $serializer->normalize($countries, 'json', [AbstractNormalizer::ATTRIBUTES => ['id','name']]);
        return $this->json(['countries'=>$countries]);

    }
}