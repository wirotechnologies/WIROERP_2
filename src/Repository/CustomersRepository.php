<?php

namespace App\Repository;

use App\Entity\Customers;
use App\Entity\CustomerTypes;
use App\Entity\IdentifierTypes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\CustomerTypesRepository;
use App\Repository\IdentifierTypesRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Doctrine\ORM\Query\ResultSetMapping;

/**
 * @extends ServiceEntityRepository<Customers>
 *
 * @method Customers|null find($id, $lockMode = null, $lockVersion = null)
 * @method Customers|null findOneBy(array $criteria, array $orderBy = null)
 * @method Customers[]    findAll()
 * @method Customers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private CustomerTypesRepository $customerTRepository, private IdentifierTypesRepository $identifierRepository)
    {
        parent::__construct($registry, Customers::class);
    }

    public function add(Customers $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Customers $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function create($customerId, $customerTypeId, $customerIdentifierType, $dataJson)
    {

        $email = isset($dataJson['email']) ? $dataJson['email']:Null ;
        $email = $dataJson['email'] != "" ? $dataJson['email']: Null;
        $customer = new Customers();
        $identifierType = $this->identifierRepository->find($customerIdentifierType);
        $customerType = $this->customerTRepository->find($customerTypeId);
        

        $customer->setPrimaryKeys($customerId, $customerType, $identifierType);
        $date = new \DateTime();
        $customer->setCreatedDate($date);
        $customer->setUpdatedDate($date);
        $customer->setEmail($email);
        
        if ($customerTypeId == 2){
            $commercialName = $dataJson['commercialName'];
            $customer->setCommercialName($commercialName);
        } 
        else{
            $firstName = $dataJson['firstName'];
            $middleName = isset($dataJson['middleName']) ? $dataJson['middleName']:Null ;
            $lastName = $dataJson['lastName'];
            $secondLastName = isset($dataJson['secondLastName']) ? $dataJson['secondLastName']:Null ;
            $customer->setFirstName($firstName);
            $customer->setMiddleName($middleName);
            $customer->setLastName($lastName);
            $customer->setSecondLastName($secondLastName);
        }
        
        return $customer;
    }


    public function update($customer, $dataJson): ?Customers
    {
        $email = $dataJson['email'] ?? null;
        $date = new \DateTime();
        if($email){
            $customer->setEmail($email);
            $customer->setUpdatedDate($date);
        }  
        return $customer;
    }

    public function jsonTest($dataJson)
    {
        $qb = $this->createQueryBuilder('c');
        $qb->select('c')
        ->where($qb->expr()->eq(
            'jsonb_each(c.data)->value',
            ':value'
        ))
        ->setParameter('value', 'item1');

        $result = $qb->getQuery()->getResult();
    }

    public function findById($id,  $customerType,  $identifierCustomerType): ?Customers
    {
       return $this->createQueryBuilder('c')
           ->join('c.customerTypes', 'ct')
           ->join('c.identifierTypes', 'ci')
           ->andWhere('c.id = :id')
           ->andWhere('ct.id = :customerTypes')
           ->andWhere('ci.id = :identifierTypes')
           ->setParameter('id', $id)
           ->setParameter('customerTypes', $customerType)
           ->setParameter('identifierTypes', $identifierCustomerType)
           ->getQuery()
           ->getOneorNullResult()
       ;
    }

    public function retrieveByIds($statementByIds)
    {
        return $this->createQueryBuilder('c')
        ->andWhere($statementByIds)
        ->getQuery()
        ->getResult();
    }

    public function retrieveCustomersByExpression($statementByExpression)
    {
        return $this->createQueryBuilder('c')
        ->andWhere($statementByExpression)
        ->getQuery()
        ->getResult();
    }


   public function findComercial($commercialName)
   {
        $qb = $this->createQueryBuilder('c')
           ->andWhere('c.commercialName = :commercialName')
           ->setParameter('commercialName', $commercialName)
        //    ->getQuery()
        //    ->getResult()
        ;
        
        return $qb->getQuery();
   }
   
   /**
    * @return Customers[] Returns an array of Customers objects
    */
    public function findCostumersById(string $customerId): array
    {
       return $this->createQueryBuilder('c')
           ->Where('c.id = :customerId')
           ->setParameter('customerId', $customerId)
           ->orderBy('c.customerTypes', 'ASC')
           ->setMaxResults(10)
           ->getQuery()
           ->getResult()
       ;
    }

   public function findByCustomerTypes($customer): array
   {
       return $this->createQueryBuilder('c')
           ->andWhere('c.customerTypes = :customerTypes')
           ->setParameter('customerTypes', $customer->getCustomerTypes())
           ->orderBy('c.customerTypes', 'ASC')
           ->setMaxResults(10)
           ->getQuery()
           ->getResult()
       ;
   }

   public function getEmails($jsonCustomersIds)
   {
    $query = "WITH json_data AS (SELECT CAST(:json AS jsonb) AS data)
    SELECT c.email
    FROM json_data, jsonb_array_elements(data->'customersIds') AS ids(clm), customers AS c
    WHERE CAST(clm->>'customersId' AS VARCHAR) = c.id
    AND CAST(clm->>'customersCustomerTypesId' AS INTEGER) = c.customer_types_id
    AND CAST(clm->>'customersIdentifierTypesId' AS INTEGER) = c.identifier_types_id
    AND c.email IS NOT NULL";
    
    $rsm = new ResultSetMapping();
    $rsm->addEntityResult('\App\Entity\Customers', 'c');
    $rsm->addScalarResult('email', 'email');
    $rsm->addMetaResult('c', 'customer_types_id', 'customer_types_id');
    $rsm->addMetaResult('c', 'identifier_types_id', 'identifier_types_id');
    
    $emails = $this->getEntityManager()->createNativeQuery($query, $rsm)
    ->setParameter('json', $jsonCustomersIds)
    ->getResult();
    return $emails;
   }

//    public function findCustomers($dataJson)
//    {
//         $expresion = isset($dataJson['expression']) ? $dataJson['expression']:Null;

//         $rows = $dataJson['rows'] ?? throw new BadRequestHttpException('400', null, 400);
      
//         $initialRow = $dataJson['initialRow'] ?? throw new BadRequestHttpException('400', null, 400);
//         $initialRow = $initialRow-1;
//         if($initialRow < 0){
//             throw new BadRequestHttpException('400', null, 400);
//         }

//         if(is_null($expresion)){
//             $customers = $this->findCustomersByRows($rows, $initialRow);
//         }
//         else{
//             $expresion = strtolower($expresion.'%');
//             $customers = $this->findByExpresion($expresion, $rows, $initialRow);       
//         }
//         return $customers;
//    }



//    public function findCustomersByRows($row, $initialRow)
//    {
//     return $this->createQueryBuilder('c')
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults($row)
//            ->setFirstResult($initialRow)
          
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findByExpresion(string $expresion, $row, $initialRow)
//    {
//     return $this->createQueryBuilder('c')
           
//            ->Where('LOWER(c.commercialName) LIKE :expresion')
//            ->OrWhere('LOWER(c.firstName) LIKE :expresion')
//            ->setParameter('expresion', $expresion)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults($row)
//            ->setFirstResult($initialRow)
          
//            ->getQuery()
//            ->getResult()
//        ;
//    }

   public function findCustomers($request)
   {
        $expresion = $request->query->get('expresion') !== (null || '""') ? $request->query->get('expresion'):Null;
        $rows = $request->query->get('rows');
        $initialRow = $request->query->get('initialRow');
        $initialRow = $initialRow-1;
        
        if(is_null($expresion) || $expresion ==""){
            $customers = $this->findByRows($rows, $initialRow);
        }
        else{
            $expresion = strtolower($expresion.'%');
            $customers = $this->findByExpresion($expresion, $rows, $initialRow);       
        }
        return $customers;
   }

   public function findByRows($row, $initialRow)
   {
    return $this->createQueryBuilder('c')
           ->orderBy('c.id', 'ASC')
           ->setMaxResults($row)
           ->setFirstResult($initialRow)
           ->getQuery()
           ->getResult()
       ;
   }

   public function findByExpresion(string $expresion, $row, $initialRow)
   {
    return $this->createQueryBuilder('c')
           ->Where('LOWER(c.commercialName) LIKE :expresion')
           ->OrWhere('LOWER(c.firstName) LIKE :expresion')
           ->setParameter('expresion', $expresion)
           ->orderBy('c.id', 'ASC')
           ->getQuery()
           ->setMaxResults($row)
           ->setFirstResult($initialRow)
           ->getResult()
       ;
   }

   public function findOnlyByExpresion(string $expression)
   {
    return $this->createQueryBuilder('c')
           ->Where('LOWER(c.commercialName) LIKE :expression')
           ->OrWhere('LOWER(c.firstName) LIKE :expression')
           ->OrWhere('LOWER(c.middleName) LIKE :expression')
           ->OrWhere('LOWER(c.lastName) LIKE :expression')
           ->OrWhere('LOWER(c.secondLastName) LIKE :expression')
           ->setParameter('expression', $expression)
           ->orderBy('c.id', 'ASC')
           ->getQuery()
           ->getResult()
       ;
   }

//    /**
//     * @return Customers[] Returns an array of Customers objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Customers
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
