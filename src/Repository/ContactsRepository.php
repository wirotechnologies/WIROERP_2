<?php

namespace App\Repository;

use App\Entity\Contacts;
use App\Entity\IdentifierTypes;
use App\Repository\IdentifierTypesRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @extends ServiceEntityRepository<Contacts>
 *
 * @method Contacts|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contacts|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contacts[]    findAll()
 * @method Contacts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private IdentifierTypesRepository $identifierRepository)
    {
        parent::__construct($registry, Contacts::class);
    } 

    public function add(Contacts $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Contacts $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function create($dataJson ): ?Contacts
    {
        
        $mainContact = $dataJson['mainContact'] ?? throw new BadRequestHttpException('400', null, 400);
        $contactId = $mainContact['identification']['value'] ?? throw new BadRequestHttpException('400', null, 400);
        $identTypeContact = $mainContact['identification']['idIdentifierType'] ?? throw new BadRequestHttpException('400', null, 400);
        $firstNameContact = $mainContact['firstName'] ?? throw new BadRequestHttpException('400', null, 400);;
        $middleNameContact = isset($mainContact['middleName']) ? $mainContact['middleName']: Null;
        $lastNameContact = $mainContact['lastName'] ?? throw new BadRequestHttpException('400', null, 400);
        $secondLastNameContact = isset($mainContact['secondLastName']) ? $mainContact['secondLastName']:Null; 
        $emailContact =  $mainContact['email'] ?? throw new BadRequestHttpException('400', null, 400);
    
        $identifierTypeContact = new IdentifierTypes();
        $identifierTypeContact = $this->identifierRepository->find($identTypeContact);
        $contact = new Contacts();
        $contact->setPrimaryKeys($contactId,$identifierTypeContact);
        $contact->setFirstName($firstNameContact);
        $contact->setMiddleName($middleNameContact);
        $contact->setLastName($lastNameContact);
        $contact->setSecondLastName($secondLastNameContact);
        $contact->setEmail($emailContact);
        $date = new \DateTime();
        $contact->setUpdateDate($date);
        $contact->setCreatedDate($date); 
        return $contact; 
        
    }

    public function update($dataJson, $contact): ?Contacts
    {
        
        $mainContact = $dataJson['mainContact'] ?? throw new BadRequestHttpException('400', null, 400);
        $contactId = $mainContact['identification']['value'] ?? throw new BadRequestHttpException('400', null, 400);
        $identTypeContact = $mainContact['identification']['idIdentifierType'] ?? throw new BadRequestHttpException('400', null, 400);
        $firstNameContact = $mainContact['firstName'] ?? throw new BadRequestHttpException('400', null, 400);;
        $middleNameContact = isset($mainContact['middleName']) ? $mainContact['middleName']: Null;
        $lastNameContact = $mainContact['lastName'] ?? throw new BadRequestHttpException('400', null, 400);
        $secondLastNameContact = isset($mainContact['secondLastName']) ? $mainContact['secondLastName']:Null; 
        $emailContact =  $mainContact['email'] ?? throw new BadRequestHttpException('400', null, 400);

        if (!is_null($firstNameContact)){
            $contact->setFirstName($firstNameContact);
            $date = new \DateTime();
            $contact->setUpdateDate($date);
            }
                    
        if (!is_null($middleNameContact)){
            $contact->setMiddleName($middleNameContact);
            $date = new \DateTime();
            $contact->setUpdateDate($date);
        }

        if (!is_null($lastNameContact)){
            $date = new \DateTime();
            $contact->setUpdateDate($date);
            $contact->setLastName($lastNameContact);
        }    

        if (!is_null($secondLastNameContact)){
            $date = new \DateTime();
            $contact->setUpdateDate($date);
            $contact->setSecondLastName($secondLastNameContact);
        }
                    
        if (!is_null($emailContact)){
            $date = new \DateTime();
            $contact->setUpdateDate($date);
            $contact->setEmail($emailContact);
        } 
        
        return($contact); 
    }

       public function findById($id, $identifierContact): ?Contacts
   {
       return $this->createQueryBuilder('c')
           ->andWhere('c.id = :id')
           ->andWhere('c.identifierTypes = :identifierTypes')
           ->setParameter('id', $id)
           ->setParameter('identifierTypes', $identifierContact)
           ->getQuery()
           ->getOneOrNullResult()
       ;
   }
  
//    /**
//     * @return Contacts[] Returns an array of Contacts objects
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

//    public function findOneBySomeField($value): ?Contacts
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
