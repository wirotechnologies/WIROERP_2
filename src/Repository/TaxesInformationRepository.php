<?php

namespace App\Repository;

use App\Entity\TaxesInformation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TaxesInformation>
 *
 * @method TaxesInformation|null find($id, $lockMode = null, $lockVersion = null)
 * @method TaxesInformation|null findOneBy(array $criteria, array $orderBy = null)
 * @method TaxesInformation[]    findAll()
 * @method TaxesInformation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaxesInformationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaxesInformation::class);
    }

    public function save(TaxesInformation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(TaxesInformation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function create($customer, $dataJson){
        $date = new \DateTime();
        //Obligaciones Tributarias
        $granContribuyente = $dataJson['granContribuyente'];
        $autorretenedor = $dataJson['autorretenedor'];
        $agenteDeRetencionIVA = $dataJson['agenteRetencionIVA'];
        $regimenSimple = $dataJson['regimenSimpleTributacion'];
        $impuestoNacionalConsumo = $dataJson['impuestoNacionalConsumo'];
        $impuestoSobreVentasIVA = $dataJson['impuestoSobreVentas'];
        //Tipo de organizacion Juridica (persona juridica o natural Segun el RUT)
        $typePerson = $dataJson['typePerson'];
        $dvNit = $dataJson['dvNit'];

        $taxesInformation = new TaxesInformation();
        $taxesInformation->setCustomers($customer);
        $taxesInformation->setDvNit($dvNit);
        $taxesInformation->setTypePerson($typePerson);
        $taxesInformation->setGranContribuyente($granContribuyente);
        $taxesInformation->setAutorretenedor($autorretenedor);
        $taxesInformation->setAgenteDeRetencionIVA($agenteDeRetencionIVA);
        $taxesInformation->setRegimenSimple($regimenSimple);
        $taxesInformation->setImpuestoNacionalConsumo($impuestoNacionalConsumo);
        $taxesInformation->setImpuestoSobreVentasIVA($impuestoSobreVentasIVA);
        $taxesInformation->setCreatedDate($date);
        $taxesInformation->setUpdatedDate($date);
        return $taxesInformation;
    }

//    /**
//     * @return TaxesInformation[] Returns an array of TaxesInformation objects
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

//    public function findOneBySomeField($value): ?TaxesInformation
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
