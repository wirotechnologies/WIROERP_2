<?php

namespace App\Repository;
use App\Entity\Cities;
use App\Entity\CustomersAddresses;
use App\Repository\CitiesRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
/**
 * @extends ServiceEntityRepository<CustomersAddresses>
 *
 * @method CustomersAddresses|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomersAddresses|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomersAddresses[]    findAll()
 * @method CustomersAddresses[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomersAddressesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private CitiesRepository $cityRepository)
    {
        parent::__construct($registry, CustomersAddresses::class);
    }

    public function add(CustomersAddresses $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CustomersAddresses $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function create($dataJson,$customer,$status): ?CustomersAddresses
    {
        $address = $dataJson['address'];
        $nameCity = $address['city'];
        $city = $this->cityRepository->findByName($nameCity);
        $line1 = $address['line1'];
        $line2 = isset($address['line2']) ? $address['line2']:Null;
        $zipcode = isset($address['zipCode']) and $address['zipCode'] != "" ? $address['zipCode']:Null;
        $socioeconomicStatus =  $address['socioeconomicStatus'];
        $note = isset($address['note']) ? $address['note']:Null;
        $date = new \DateTime();
        $customerAddress = new CustomersAddresses();
        $customerAddress->setCustomers($customer);
        $customerAddress->setCities($city);
        $customerAddress->setLine1($line1);
        $customerAddress->setLine2($line2);
        $customerAddress->setZipcode($zipcode);
        $customerAddress->setSocioeconomicStatus($socioeconomicStatus);
        $customerAddress->setNote($note);
        $customerAddress->setStatus($status);
        $customerAddress->setCreatedDate($date);
        return $customerAddress;
    }

    public function createCustomerAddress($address,$customer,$city,$status): ?CustomersAddresses
    {
        $line1 = $address['line1'] ?? null;
        $line2 = $address['line2'] ?? null;
        $note = $address['note'] ?? null;
        $zipcode = $address['zipcode'] ?? null;
        $socioeconomicStatus = $address['socioeconomicStatus'] ?? null;

        $customerAddress = new CustomersAddresses();
        $date = new \DateTime();
        $customerAddress->setCustomers($customer);
        $customerAddress->setStatus($status);
        $customerAddress->setCities($city);
        $customerAddress->setLine1($line1);
        $customerAddress->setLine2($line2);
        $customerAddress->setNote($note);
        $customerAddress->setZipcode($zipcode);
        $customerAddress->setSocioeconomicStatus($socioeconomicStatus);
        $customerAddress->setCreatedDate($date);
        $customerAddress->setUpdatedDate($date);
        return $customerAddress;


    }

    public function updateStatus($customerAddress,$status)
    {
        $date = new \DateTime();
        $customerAddress->setStatus($status);
        $customerAddress->setUpdatedDate($date);
        return $customerAddress;
    }

    public function update($dataJson, $customerAddress): ?CustomersAddresses
    {
        $address = $dataJson['address'];

        $nameCity = isset($address['city']) ? $address['city']:Null;
        if (!is_null($nameCity)){
            $cityCustomer = $this->cityRepository->findByName($nameCity);
            $customerAddress->setCities($cityCustomer);
        }

        $line1 = isset($address['line1']) ? $address['line1']:Null;
        if (!is_null($line1)){
            $customerAddress->setLine1($line1);
        }
                
        $line2 = isset($dataJson['address']['line2']) ? $dataJson['address']['line2']:Null;
        if (!is_null($line2)){
            $customerAddress->setLine2($line2);
        }
                
        $zipcode = isset($dataJson['address']['zipcode']) ? $dataJson['address']['zipcode']:Null;
        if (!is_null($zipcode)){
            $customerAddress->setZipcode($zipcode);
        }

        $socioeconomicStatus =  isset($address['socioeconomicStatus']) ? $address['socioeconomicStatus']:Null;
        if (!is_null($socioeconomicStatus)){
            $customerAddress->setSocioeconomicStatus($socioeconomicStatus);
        }
            
        $note = isset($dataJson['address']['note']) ? $dataJson['address']['note']:Null;
        if (!is_null($note)){
            $customerAddress->setNote($note);
        }
        return $customerAddress;
    }

    /**
    * @return CustomersAddresses[] Returns an array of CustomersPhones objects
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

   public function findOneByCustomer($customer): ?CustomersAddresses
   {
       return $this->createQueryBuilder('ca')
        ->join('ca.customers', 'c')
        ->andWhere('c.id = :id')
        ->andWhere('c.customerTypes = :customerTypes')
        ->andWhere('c.identifierTypes = :identifierTypes')
        ->setParameter('id',  $customer->getId())
        ->setParameter('customerTypes', $customer->getCustomerTypes())
        ->setParameter('identifierTypes', $customer->getIdentifierTypes())
        ->setMaxResults(1)
        ->getQuery()
        ->getOneOrNullResult()
       ;
   }

//    /**
//     * @return CustomersAddresses[] Returns an array of CustomersAddresses objects
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

//    public function findOneBySomeField($value): ?CustomersAddresses
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
