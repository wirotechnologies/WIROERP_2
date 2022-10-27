<?php
namespace App\Controller;

use App\Service\RequestValidator\RequestValidator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Psr\Log\LoggerInterface;


class HelloController extends AbstractController
{
    private $logger;
    public function __construct(LoggerInterface $logger)
    {
       
        $this->logger = $logger;
    }
    public function __invoke(Request $request, LoggerInterface $logger): Response
    {
        $this->logger->info("ENTRO");
        
        $array = [1,2,3];
        return new JsonResponse(json_encode($array),Response::HTTP_OK);
    }

}