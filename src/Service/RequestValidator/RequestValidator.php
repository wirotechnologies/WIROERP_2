<?php

namespace App\Service\RequestValidator;
use App\Repository\ContactsRepository;
use phpDocumentor\Reflection\DocBlock\Tags\Formatter\AlignFormatter;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Psr\Log\LoggerInterface;
class RequestValidator
{
    public function __construct(
        private ContactsRepository $contactRepository,
        private ValidatorInterface $validator,
        private LoggerInterface $logger
    )
    {
    }
    public function validateRequestCreateCustomer($dataJson, Request $request)
    {
        // $uploadFileEnergyInvoice = $request->files->get('fileEnergyInvoice') ?? throw new BadRequestHttpException('400 File Energy Invoice not upload', null, 400);
        // $uploadFileIdentificationDocument = $request->files->get('identificationDocument') ?? throw new BadRequestHttpException('400 File Identification Document not upload', null, 400);
        
        //$email = $dataJson['email'] ?? throw new BadRequestHttpException('400', null, 400);
        $customerType =  $dataJson['customerType'];
        if($customerType == 2){
            // $uploadFileIdentificationDocument = $request->files->get('fileCamaraComercio') ?? throw new BadRequestHttpException('400 File Camara Comercio not upload', null, 400);
            // $uploadFileRUT = $request->files->get('fileRUT') ?? throw new BadRequestHttpException('400 File RUT not upload', null, 400);
            // $commercialName = $dataJson['commercialName'] ?? throw new BadRequestHttpException('400', null, 400);
            
            //Obligaciones Tributarias
            $granContribuyente = $dataJson['taxesInformation']['granContribuyente'] ?? throw new BadRequestHttpException('400', null, 400);
            $autorretenedor = $dataJson['taxesInformation']['autorretenedor'] ?? throw new BadRequestHttpException('400', null, 400);
            $agenteDeRetencionIVA = $dataJson['taxesInformation']['agenteRetencionIVA'] ?? throw new BadRequestHttpException('400', null, 400);
            $regimenSimple = $dataJson['taxesInformation']['regimenSimpleTributacion'] ?? throw new BadRequestHttpException('400', null, 400);
            $impuestoNacionalConsumo = $dataJson['taxesInformation']['impuestoNacionalConsumo'] ?? throw new BadRequestHttpException('400', null, 400);
            $impuestoSobreVentasIVA = $dataJson['taxesInformation']['impuestoSobreVentas'] ?? throw new BadRequestHttpException('400', null, 400);
            //Tipo de organizacion Juridica (persona juridica o natural Segun el RUT)
            $typePerson = $dataJson['taxesInformation']['typePerson'] ?? throw new BadRequestHttpException('400', null, 400);
            $customerIdentifierType =  $dataJson['identification']['idIdentifierType'];
            if($customerIdentifierType == 2){
                $dvNit = $dataJson['taxesInformation']['dvNit'] ?? throw new BadRequestHttpException('400', null, 400);
                if(!is_numeric($dvNit)){
                    throw new BadRequestHttpException('400', null, 400);
                }
            }
            
            $mainContact = $dataJson['mainContact'] ?? throw new BadRequestHttpException('400', null, 400);
            $contactId = $mainContact['identification']['value'] ?? throw new BadRequestHttpException('400', null, 400);
            $identTypeContact = $mainContact['identification']['idIdentifierType'] ?? throw new BadRequestHttpException('400', null, 400);
            if(!is_numeric($contactId) or !is_numeric($identTypeContact)){
                throw new BadRequestHttpException('400', null, 400);
            }
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
        foreach($phoneNumbers as $phoneNumber){
            if(!is_numeric($phoneNumber)){
                throw new BadRequestHttpException('400', null, 400);
            }
        }
        $nameCountry = $dataJson['address']['country'] ?? throw new BadRequestHttpException('400', null, 400);
        
        $references = $dataJson['references'] ?? throw new BadRequestHttpException('400', null, 400);
        foreach($references as $reference){
            $fullNameReference = $reference['fullName'] ?? throw new BadRequestHttpException('400', null, 400);
            $phoneReference = $reference['contactPhone'] ?? throw new BadRequestHttpException('400', null, 400);
            if(!is_numeric($phoneReference)){
                throw new BadRequestHttpException('400', null, 400);
            }
        }
        
        return 'OK';
    }

    public function validateRequestUpdateCustomer($dataJson)
    {
        $email = $dataJson['email'] ?? throw new BadRequestHttpException('400', null, 400);
        $customerType =  $dataJson['customerType'];
        if($customerType == 2){
            $commercialName = $dataJson['commercialName'] ?? throw new BadRequestHttpException('400', null, 400);
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

    public function validateUpdateCustomer($request)
    {
        $requestData = $request->get('request') ?? null;
        if($requestData){
            $dataJson = json_decode($requestData,true) ?? null;
            $constraint =
            new Assert\Collection([
                'middleName' => new Assert\Optional(),
                'secondLastName' => new Assert\Optional(),
                'email' => new Assert\Optional(),
                'phoneNumbers' => new Assert\Optional(new Assert\All([new Assert\Type('numeric')])),
                'address' => new Assert\Optional(new Assert\Collection([
                    'cityId' => [new Assert\NotBlank(),new Assert\Type('integer')],
                    'line1' => [new Assert\NotBlank(),new Assert\Length(max:256)],
                    'line2' => [new Assert\NotBlank(),new Assert\Length(max:128)],
                    'note' => [new Assert\NotBlank(),new Assert\Length(max:256)],
                    'zipcode' => [new Assert\NotBlank(),],
                    'socioeconomicStatus' => [new Assert\NotBlank(),new Assert\Choice(['1','2','3','4','5','6','Comercial'])]
                ])),
                'references' => new Assert\Optional(new Assert\Collection([
                    'fullName'=> [new Assert\NotBlank(),new Assert\Length(max:256)],
                    'phoneNumber' => [new Assert\NotBlank(),new Assert\Type('numeric')],
                    'typeReference' => [new Assert\NotBlank(),new Assert\Choice(['Personal del Representante Legal','Personal','Familiar'])]
                ])),
                'mainContact' => new Assert\Optional(new Assert\Collection([
                    'firstName' => [new Assert\NotBlank(),new Assert\Length(max:128)],
                    'middleName' => new Assert\Optional([new Assert\NotBlank(),new Assert\Length(max:128)]),
                    'lastName' => [new Assert\NotBlank(),new Assert\Length(max:128)],
                    'secondLastName' => new Assert\Optional([new Assert\NotBlank(),new Assert\Length(max:128)]),
                    'email' => [new Assert\NotBlank(),new Assert\Email()],
                ])),
                'taxesInformation' => new Assert\Optional(new Assert\Collection([
                    'granContribuyente' => new Assert\Optional([new Assert\Type('boolean')]),
                    'autorretenedor' => new Assert\Optional([new Assert\Type('boolean')]),
                    'agenteRetencionIVA' => new Assert\Optional([new Assert\Type('boolean')]),
                    'regimenSimpleTributacion' => new Assert\Optional([new Assert\Type('boolean')]),
                    'impuestoNacionalConsumo' => new Assert\Optional([new Assert\Type('boolean')]),
                    'impuestoSobreVentas' => new Assert\Optional([new Assert\Type('boolean')]),
                    'taxTypePersonId' => new Assert\Optional([new Assert\NotBlank(),new Assert\Type('integer')]),
                    'dvNit' => new Assert\Optional([new Assert\Type('integer')])
                ]))
            ],null,null,true);

            $violations = $this->validator->validate($dataJson, $constraint);

            if(count($violations)>0){
                $exception = new BadRequestHttpException('Verifique que todos los campos requeridos sean válidos');
                $exception =  FlattenException::create($exception);
                $errorsString = (string) $violations;
                $this->logger->error("BadRequestErrorUpdateContract: validator = {$errorsString}");
                return $exception;
            }
        }
        
        $constraint = new Assert\Collection([
            'fileEnergyInvoice' => new Assert\Optional(new Assert\File(maxSize: '2M',mimeTypes: ['application/pdf','image/png','image/jpeg','application/msword'])),
            'identificationDocument'  => new Assert\Optional(new Assert\File(maxSize: '2M',mimeTypes: ['application/pdf','image/png','image/jpeg','application/msword'])),
            'fileCamaraComercio'  => new Assert\Optional(new Assert\File(maxSize: '2M',mimeTypes: ['application/pdf','image/png','image/jpeg','application/msword'])),
            'fileRUT'  => new Assert\Optional(new Assert\File(maxSize: '2M',mimeTypes: ['application/pdf','image/png','image/jpeg','application/msword'])),
        ],null,null,true);
        $violations = $this->validator->validate($request->files->all(), $constraint);

        if(count($violations)>0){
            $exception = new BadRequestHttpException('Verifique que los archivos sean válidos');
            $exception =  FlattenException::create($exception);
            $errorsString = (string) $violations;
            $this->logger->error("BadRequestErrorUpdateContract: validator = {$errorsString}");
            return $exception;
        }
        
        return null;
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

    public function validateRequestRetrieveCustomersByExpression($request)
    {
        $expression = $request->query->get('expression') ?? throw new BadRequestHttpException('400', null, 400);
        return 'OK';
    }

}