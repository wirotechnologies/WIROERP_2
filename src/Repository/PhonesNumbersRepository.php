<?php

namespace App\Repository ;

use App\Entity\PhonesNumbers;
use App\Repository\CountriesRepository;
use App\Repository\CountriesPhoneCodeRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @extends ServiceEntityRepository<PhonesNumbers>
 *
 * @method PhonesNumbers|null find($id, $lockMode = null, $lockVersion = null)
 * @method PhonesNumbers|null findOneBy(array $criteria, array $orderBy = null)
 * @method PhonesNumbers[]    findAll()
 * @method PhonesNumbers[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PhonesNumbersRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry,  private CountriesRepository $countryRepository,private CountriesPhoneCodeRepository $countryPhoneRepository)
    {
        parent::__construct($registry, PhonesNumbers::class);
    }

    public function add(PhonesNumbers $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PhonesNumbers $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function create($phoneNumber, $countryPhoneCode) :?PhonesNumbers
    {
        $number = new PhonesNumbers();
        $date = new \DateTime();
        $number->setPhoneNumber($phoneNumber);
        $number->setCountriesPhoneCode($countryPhoneCode);
        $number->setCreatedDate($date);    
        return $number;
        
    }

//    /**
//     * @return PhonesNumbers[] Returns an array of PhonesNumbers objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?PhonesNumbers
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
