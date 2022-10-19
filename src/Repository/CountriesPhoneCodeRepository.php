<?php

namespace App\Repository;

use App\Entity\CountriesPhoneCode;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CountriesPhoneCode>
 *
 * @method CountriesPhoneCode|null find($id, $lockMode = null, $lockVersion = null)
 * @method CountriesPhoneCode|null findOneBy(array $criteria, array $orderBy = null)
 * @method CountriesPhoneCode[]    findAll()
 * @method CountriesPhoneCode[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CountriesPhoneCodeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CountriesPhoneCode::class);
    }

    public function findOneByCountry($countryId): ?CountriesPhoneCode
   {
       return $this->createQueryBuilder('c')
           ->andWhere('c.countries = :countryId')
           ->setParameter('countryId', $countryId)
           ->getQuery()
           ->getOneOrNullResult()
       ;
   }

    public function add(CountriesPhoneCode $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CountriesPhoneCode $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return CountriesPhoneCode[] Returns an array of CountriesPhoneCode objects
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

//    public function findOneBySomeField($value): ?CountriesPhoneCode
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
