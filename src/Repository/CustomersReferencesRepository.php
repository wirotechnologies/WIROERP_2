<?php

namespace App\Repository;

use App\Entity\CustomersReferences;
use App\Entity\IdentifierTypes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\IdentifierTypesRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @extends ServiceEntityRepository<CustomersReferences>
 *
 * @method CustomersReferences|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomersReferences|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomersReferences[]    findAll()
 * @method CustomersReferences[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomersReferencesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry,  private IdentifierTypesRepository $identifierRepository)
    {
        parent::__construct($registry, CustomersReferences::class);
    }

    public function add(CustomersReferences $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CustomersReferences $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function create($reference,$customer,$countryPhoneCode,$status) : ?CustomersReferences
    {
        $fullNameReference = $reference['fullName'] ?? throw new BadRequestHttpException('400', null, 400);
        $phoneReference = $reference['contactPhone'] ?? throw new BadRequestHttpException('400', null, 400);
        $typeReference = isset($reference['typeReference']) ? $reference['typeReference']:Null;
        $customerReference = new CustomersReferences();
        $date = new \DateTime();
        $customerReference->setCustomers($customer);
        $customerReference->setTypeReference($typeReference);
        $customerReference->setFullName($fullNameReference);
        $customerReference->setPhoneNumber($phoneReference);
        $customerReference->setReferencesCountriesPhoneCode($countryPhoneCode);
        $customerReference->setStatus($status);
        $customerReference->setCreatedDate($date);
        
        return $customerReference;    
        
    }

    public function createCustomerReference($reference,$customer,$countryPhoneCode,$status) : ?CustomersReferences
    {
        $fullName = $reference['fullName'];
        $phoneNumber = $reference['phoneNumber'];
        $typeReference = $reference['typeReference'];
        
        $customerReference = new CustomersReferences();
        $date = new \DateTime();
        $customerReference->setCustomers($customer);
        $customerReference->setTypeReference($typeReference);
        $customerReference->setFullName($fullName);
        $customerReference->setPhoneNumber($phoneNumber);
        $customerReference->setReferencesCountriesPhoneCode($countryPhoneCode);
        $customerReference->setStatus($status);
        $customerReference->setCreatedDate($date);
        $customerReference->setUpdatedDate($date);
        
        return $customerReference;    
    }

    public function updateStatus($customerReference,$status)
    {   
        $date = new \DateTime();
        $customerReference->setStatus($status);
        $customerReference->setUpdatedDate($date);
        return $customerReference;
    }

    /**
    * @return CustomersAddresses[] Returns an array of CustomersPhones objects
    */
    public function findByCustomer($customer): array
    {
        return $this->createQueryBuilder('cr')
        ->join('cr.customers', 'c')
        ->andWhere('c.id = :id')
        ->andWhere('c.customerTypes = :customerTypes')
        ->andWhere('c.identifierTypes = :identifierTypes')
        ->setParameter('id',  $customer->getId())
        ->setParameter('customerTypes', $customer->getCustomerTypes())
        ->setParameter('identifierTypes', $customer->getIdentifierTypes())
        ->getQuery()
        ->getResult()
       ;
    }

//    /**
//     * @return CustomersReferences[] Returns an array of CustomersReferences objects
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

//    public function findOneBySomeField($value): ?CustomersReferences
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
