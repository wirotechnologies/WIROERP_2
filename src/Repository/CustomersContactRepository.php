<?php

namespace App\Repository;

use App\Entity\CustomersContact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CustomersContact>
 *
 * @method CustomersContact|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomersContact|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomersContact[]    findAll()
 * @method CustomersContact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomersContactRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomersContact::class);
    }

    public function add(CustomersContact $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CustomersContact $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function create($customer, $contact,$status): ?CustomersContact
    {
        $customerContact = new CustomersContact();
        $customerContact->setCustomers($customer);
        $customerContact->setContacts($contact);
        $customerContact->setStatus($status);
        return $customerContact;
    }

    public function update($contact, $customerContact): ?CustomersContact
    {
        $customerContact->setContacts($contact);
        return $customerContact;
    }

       public function findOneByCustomer($customer): ?CustomersContact
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
        ->getOneOrNullResult()
       ;
   }

   public function findByCustomer($customer)
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

//    /**
//     * @return CustomersContact[] Returns an array of CustomersContact objects
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

//    public function findOneBySomeField($value): ?CustomersContact
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
