<?php

namespace App\Controller;

use App\Repository\CustomersFilesRepository;
use App\Service\Files\UploadFiles;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class GetCustomerFileByCustomerFileIdController extends AbstractController
{

    public function __construct(
        private CustomersFilesRepository $customersFilesRepository,
        private UploadFiles $files
    )
    {
    }

    public function getFileByCustomerFileId(int $customerFileId)
    {
        $customerFile = $this->customersFilesRepository->find($customerFileId);
        if(!$customerFile){
            return $this->json(['message'=>'No se encontró archivo']);
        }
        $filename = $customerFile->getFileName();
        $filePath = $this->getParameter('customers_uploads').'/'.$filename;
        $file = new File($filePath);
        $mimeType = $file->getMimeType();
        $response = new Response();
        $response->headers->set('Content-Type', $mimeType);
        $response->headers->set('Content-Disposition', 'inline; filename='.$filename);
        $response->setContent(file_get_contents($filePath));
        return $response;
    }
}