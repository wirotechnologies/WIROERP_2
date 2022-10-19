<?php

namespace App\Repository;

use App\Entity\CustomerTypes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CustomerTypes>
 *
 * @method CustomerTypes|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomerTypes|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomerTypes[]    findAll()
 * @method CustomerTypes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomerTypesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomerTypes::class);
    }

    public function add(CustomerTypes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CustomerTypes $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneById($id): ?CustomerTypes
    {
        return $this->createQueryBuilder('c')
           ->andWhere('c.id = :id')
           ->setParameter('id', $id)
           ->getQuery()
           ->getOneOrNullResult()
        ;
    }

//    /**
//     * @return CustomerTypes[] Returns an array of CustomerTypes objects
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

//    public function findOneBySomeField($value): ?CustomerTypes
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
