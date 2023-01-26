<?php

namespace App\Repository;

use App\Entity\CustomersFiles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CustomersFiles>
 *
 * @method CustomersFiles|null find($id, $lockMode = null, $lockVersion = null)
 * @method CustomersFiles|null findOneBy(array $criteria, array $orderBy = null)
 * @method CustomersFiles[]    findAll()
 * @method CustomersFiles[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CustomersFilesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CustomersFiles::class);
    }

    public function save(CustomersFiles $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(CustomersFiles $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function create($newFilename,$customer,$status)
    {
        $uploadedFile = new CustomersFiles();
        $uploadedFile->setFileName($newFilename);
        $uploadedFile->setCustomers($customer);
        $uploadedFile->setStatus($status);
        return $uploadedFile;
    }

//    /**
//     * @return CustomersFiles[] Returns an array of CustomersFiles objects
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

//    public function findOneBySomeField($value): ?CustomersFiles
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
