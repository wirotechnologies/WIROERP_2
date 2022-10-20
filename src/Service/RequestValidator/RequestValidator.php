<?php

namespace App\Service\RequestValidator;
use App\Repository\ContactsRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
class RequestValidator
{
    public function __construct(
        private ContactsRepository $contactRepository
    )
    {
    }
    public function validateRequestCreateCustomer($dataJson)
    {
        $customerId=  $dataJson['identification']["value"] ?? throw new BadRequestHttpException('400', null, 400);
        $customerType=  $dataJson['customerType'] ?? throw new BadRequestHttpException('400', null, 400);
        $customerIdentifierType =  $dataJson['identification']['idIdentifierType'] ?? throw new BadRequestHttpException('400', null, 400);
        $email = $dataJson['email'] ?? throw new BadRequestHttpException('400', null, 400);
        if($customerType == 2){
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
}