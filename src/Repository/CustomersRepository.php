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

    public function create($customerId, $customerTypeId, $customerIdentifierType, $dataJson): ?Customers
    {

        $email = $dataJson['email'];

        $customer = new Customers();
        $identifierType = $this->identifierRepository->find($customerIdentifierType);
        $customerType = $this->customerTRepository->find($customerTypeId);
        $customer->setPrimaryKeys($customerId, $customerType, $identifierType);
        
        $date = new \DateTime();
        $customer->setCreatedDate($date);
        $customer->setUpdatedDate($date);

        $customer->setEmail($email);

        if ($customerTypeId == 2){
            $comercialName = $dataJson['comercialName'];
            $customer->setComercialName($comercialName);
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
        $custType = $customer->getCustomerTypes();
        $email = $dataJson['email'] ?? throw new BadRequestHttpException('400', null, 400);
        
        if (!is_null($email)){   
            $customer->setEmail($email);
            $date = new \DateTime();
            $customer->setUpdatedDate($date);  
        }

        if($custType->getId() == 2 ){
            $comercialName = $dataJson['comercialName'] ?? throw new BadRequestHttpException('400', null, 400);
            if (!is_null($comercialName)){
                $customer->setComercialName($comercialName);
                $date = new \DateTime();
                $customer->setUpdatedDate($date);
            }
        }

        else{
            $firstName = $dataJson['firstName'] ?? throw new BadRequestHttpException('400', null, 400);
            $middleName = isset($dataJson['middleName']) ? $dataJson['middleName']:Null;
            $lastName = $dataJson['lastName'] ?? throw new BadRequestHttpException('400', null, 400);
            $secondLastName = isset($dataJson['secondLastName']) ? $dataJson['secondLastName']:Null;

            if (!is_null($firstName)){
                $customer->setFirstName($firstName);
                $date = new \DateTime();
                $customer->setUpdatedDate($date);
            }
            if (!is_null($middleName)){
                $customer->setMiddleName($middleName);
                $date = new \DateTime();
                $customer->setUpdatedDate($date);
            } 
            if (!is_null($lastName)){
                $customer->setLastName($lastName);
                $date = new \DateTime();
                $customer->setUpdatedDate($date);
            }
            if (!is_null($secondLastName)){
                $customer->setSecondLastName($secondLastName);
                $date = new \DateTime();
                $customer->setUpdatedDate($date);
            }
        }    
        return $customer;
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


   public function findOneById($id): ?Customers
   {
       return $this->createQueryBuilder('c')
           ->andWhere('c.id = :id')
           ->setParameter('id', $id)
           ->getQuery()
           ->getOneOrNullResult()
        ;
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
