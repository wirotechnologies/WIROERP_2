<?php
namespace App\Controller;
use App\Repository\CustomersRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Psr\Log\LoggerInterface;
use Doctrine\Persistence\ManagerRegistry;

use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

class GetCustomersEmail extends AbstractController
{
    public function __construct(
        private CustomersRepository $customersRepository
    )
    {}

    public function getEmails(SerializerInterface $serializer,Request $request, LoggerInterface $logger,ManagerRegistry $doctrine)
    {
        $entityManager = $doctrine->getManager();
        $json = $request->getContent();

        $emails = $this->customersRepository->getEmails($json);
        if(!$emails){
            $response = new JsonResponse();
            $response->setContent(json_encode(['message'=>'No hay emails']));
            $response->setStatusCode(404);
            return $response;
        }
        return $this->json([
            'emails' => $emails
        ]);
    
        
    }

}