<?php

namespace App\Repository;

use App\Entity\CustomersPhones;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CustomersPhones>
 *
 * @method CustomersPhones|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomersPhones|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomersPhones[]    findAll()
 * @method CustomersPhones[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomersPhonesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomersPhones::class);
    }

    public function add(CustomersPhones $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CustomersPhones $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function create($number, $countryPhoneCode, $customer) :?CustomersPhones
    {
        $customerPhone = new CustomersPhones();
        $date = new \DateTime();
        $customerPhone->setPhonesNumber($number);
        $customerPhone->setCountriesPhoneCode($countryPhoneCode);
        $customerPhone->setCustomers($customer);
        $customerPhone->setCreatedDate($date);
        return $customerPhone;
        
    }

    /**
    * @return CustomersPhones[] Returns an array of CustomersPhones objects
    */
    public function findByCustomer($customer): array
    {
        return $this->createQueryBuilder('cc')
        ->join('cc.customers', 'c')
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

    public function findOneByCustomer($customer): ?CustomersPhones
    {
        return $this->createQueryBuilder('pp')
         ->join('cp.customers', 'c')
         ->andWhere('c.id = :id')
         ->andWhere('c.customerTypes = :customerTypes')
         ->andWhere('c.identifierTypes = :identifierTypes')
         ->setParameter('id',  $customer->getId())
         ->setParameter('customerTypes', $customer->getCustomerTypes())
         ->setParameter('identifierTypes', $customer->getIdentifierTypes())
         ->getQuery()
         ->getOneOrNullResult()
        ;
    }

//    /**
//     * @return CustomersPhones[] Returns an array of CustomersPhones objects
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

//    public function findOneBySomeField($value): ?CustomersPhones
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
