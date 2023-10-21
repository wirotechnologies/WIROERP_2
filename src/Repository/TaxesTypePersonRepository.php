<?php

namespace App\Repository;

use App\Entity\TaxesTypePerson;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TaxesTypePerson>
 *
 * @method TaxesTypePerson|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaxesTypePerson|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaxesTypePerson[]    findAll()
 * @method TaxesTypePerson[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaxesTypePersonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaxesTypePerson::class);
    }

    public function save(TaxesTypePerson $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TaxesTypePerson $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function filter($typeProductId=null,$initialRow=null,$rows=null)
    {
        $queryBuilder = $this->createQueryBuilder('t');
        $queryBuilder = $this->query(
            $queryBuilder,
            $typeProductId,
        );
        if($initialRow){
            $initialRow-=1;
            $queryBuilder->setFirstResult($initialRow);
        }
        if($rows){
            $queryBuilder->setMaxResults($rows);
        }

        return $queryBuilder->orderBy('t.id', 'ASC')
            ->getQuery()
            ->getResult()
            ;
    }

    public function countResults($typeProductId=null)
    {
        $queryBuilder = $this->createQueryBuilder('t')
            ->select('COUNT(t.id)');
        $queryBuilder = $this->query(
            $queryBuilder,
            $typeProductId,
        );
        return $queryBuilder->getQuery()->getSingleScalarResult();
    }

    public function query($queryBuilder,$typeProductId)
    {
        if($typeProductId){
            $queryBuilder->andWhere('t.id IN (:typeProductId)')
                ->setParameter('typeProductId',$typeProductId);
        }
        return $queryBuilder;
    }

//    /**
//     * @return TaxesTypePerson[] Returns an array of TaxesTypePerson objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?TaxesTypePerson
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
