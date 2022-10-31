<?php

namespace App\Service\RequestValidator;
use App\Repository\ContactsRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\Request;
class RequestValidator
{
    public function __construct(
        private ContactsRepository $contactRepository
    )
    {
    }
    public function validateRequestCreateCustomer($dataJson, Request $request)
    {

        //$dataJson = json_decode($request->get('request'), true);
        //dd($dataJson);
        $uploadFileEnergyInvoice = $request->files->get('fileEnergyInvoice') ?? throw new BadRequestHttpException('400 File Energy Invoice not upload', null, 400);
        $uploadFileIdentificationDocument = $request->files->get('identificationDocument') ?? throw new BadRequestHttpException('400 File Identification Document not upload', null, 400);
        $customerId =  $dataJson['identification']["value"];
        // ?? throw new BadRequestHttpException('400', null, 400);
        $customerType =  $dataJson['customerType'] ?? throw new BadRequestHttpException('400', null, 400);
        $customerIdentifierType =  $dataJson['identification']['idIdentifierType'] ?? throw new BadRequestHttpException('400', null, 400);
        $email = $dataJson['email'] ?? throw new BadRequestHttpException('400', null, 400);
        
        if($customerType == 2){
            $uploadFileIdentificationDocument = $request->files->get('fileCamaraComercio') ?? throw new BadRequestHttpException('400 File Camara Comercio not upload', null, 400);
            $comercialName = $dataJson['comercialName'] ?? throw new BadRequestHttpException('400', null, 400);
            $mainContact = $dataJson['mainContact'] ?? throw new BadRequestHttpException('400', null, 400);
            $contactId = $mainContact['identification']['value'] ?? throw new BadRequestHttpException('400', null, 400);
            $identTypeContact = $mainContact['identification']['idIdentifierType'] ?? throw new BadRequestHttpException('400', null, 400);

            $firstNameContact = $mainContact['firstName'] ?? throw new BadRequestHttpException('400', null, 400);;
            $lastNameContact = $mainContact['lastName'] ?? throw new BadRequestHttpException('400', null, 400);
            $emailContact =  $mainContact['email'] ?? throw new BadRequestHttpException('400', null, 400);
        }
        else{
            $firstName = $dataJson['firstName'] ?? throw new BadRequestHttpException('400', null, 400);
            $lastName = $dataJson['lastName'] ?? throw new BadRequestHttpException('400', null, 400);
        }

        $address = $dataJson['address'] ?? throw new BadRequestHttpException('400', null, 400);
        $nameCity = $address['city'] ?? throw new BadRequestHttpException('400', null, 400);
        $line1 = $address['line1'] ?? throw new BadRequestHttpException('400', null, 400);
        $socioeconomicStatus =  $address['socioeconomicStatus'] ?? throw new BadRequestHttpException('400', null, 400);

        $phoneNumbers = $dataJson['phoneNumbers'] ?? throw new BadRequestHttpException('400', null, 400);
        $nameCountry = $dataJson['address']['country'] ?? throw new BadRequestHttpException('400', null, 400);
        
        $references = $dataJson['references'] ?? throw new BadRequestHttpException('400', null, 400);
        foreach($references as $reference){
            $fullNameReference = $reference['fullName'] ?? throw new BadRequestHttpException('400', null, 400);
            $phoneReference = $reference['contactPhone'] ?? throw new BadRequestHttpException('400', null, 400);
            $idTypeReference = $reference['type'] ?? throw new BadRequestHttpException('400', null, 400);
        }
        
        return 'OK';
    }

    public function validateRequestUpdateCustomer($dataJson)
    {
        $customerId=  $dataJson['identification']["value"] ?? throw new BadRequestHttpException('400', null, 400);
        $customerType=  $dataJson['customerType'] ?? throw new BadRequestHttpException('400', null, 400);
        $customerIdentifierType =  $dataJson['identification']['idIdentifierType'] ?? throw new BadRequestHttpException('400', null, 400);
        $mainContact = isset($dataJson['mainContact']) ? $dataJson['mainContact']:Null;
        if($customerType == 2 and !is_null($mainContact)){
            $contactId = $mainContact['identification']['value'] ?? throw new BadRequestHttpException('400', null, 400);
            $identTypeContact = $mainContact['identification']['idIdentifierType'] ?? throw new BadRequestHttpException('400', null, 400);
            $contact = $this->contactRepository->findById($contactId, $identTypeContact);
            if($contact == Null){
                $firstNameContact = $mainContact['firstName'] ?? throw new BadRequestHttpException('400', null, 400);
                $lastNameContact = $mainContact['lastName'] ?? throw new BadRequestHttpException('400', null, 400);
                $emailContact =  $mainContact['email'] ?? throw new BadRequestHttpException('400', null, 400);
            }    
        }

        return 'OK';
    }

    public function validateRequestRetrieveCustomers($request)
    {
        $rows = $request->query->get('rows') ?? throw new BadRequestHttpException('400', null, 400);
        $initialRow = $request->query->get('initialRow') ?? throw new BadRequestHttpException('400', null, 400);
        $initialRow = $initialRow-1;
        if($initialRow < 0){
            throw new BadRequestHttpException('400', null, 400);
        }

        return 'OK';
    }

}