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
        $granContribuyente = $dataJson['taxesInformation']['granContribuyente'];
        $autorretenedor = $dataJson['taxesInformation']['autorretenedor'];
        $agenteDeRetencionIVA = $dataJson['taxesInformation']['agenteRetencionIVA'];
        $regimenSimple = $dataJson['taxesInformation']['regimenSimpleTributacion'];
        $impuestoNacionalConsumo = $dataJson['taxesInformation']['impuestoNacionalConsumo'];
        $impuestoSobreVentasIVA = $dataJson['taxesInformation']['impuestoSobreVentas'];
        //Tipo de organizacion Juridica (persona juridica o natural Segun el RUT)
        $typePerson = $dataJson['taxesInformation']['typePerson'];
        $dvNit = isset($dataJson['taxesInformation']['dvNit']) ? $dataJson['taxesInformation']['dvNit'] : Null;
        
        $taxesInformation = new TaxesInformation();

        $taxesInformation->setCustomers($customer);
        if($customer->getIdentifierTypes()==2){
            $taxesInformation->setDvNit($dvNit);
        }
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

    public function createTaxesInformation($taxesInformation,$taxTypePerson,$customer){

        $granContribuyente = $taxesInformation['granContribuyente'] ?? null;
        $autorretenedor = $taxesInformation['autorretenedor'] ?? null;
        $agenteRetencionIVA = $taxesInformation['agenteRetencionIVA'] ?? null;
        $regimenSimpleTributacion = $taxesInformation['regimenSimpleTributacion'] ?? null;
        $impuestoNacionalConsumo = $taxesInformation['impuestoNacionalConsumo'] ?? null;
        $impuestoSobreVentas = $taxesInformation['impuestoSobreVentas'] ?? null;
        $dvNit = $taxesInformation['dvNit'] ?? null;
        $date = new \DateTime();
        $taxesInformation = new TaxesInformation();
        $taxesInformation->setCustomers($customer);
        $taxesInformation->setTaxesTypePerson($taxTypePerson);
        $taxesInformation->setDvNit($dvNit);
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


    public function updateTaxesInformation($taxesInformation,$customerTaxesInformation,$taxTypePerson){
        $granContribuyente = $taxesInformation['granContribuyente'] ?? null;
        $autorretenedor = $taxesInformation['autorretenedor'] ?? null;
        $agenteRetencionIVA = $taxesInformation['agenteRetencionIVA'] ?? null;
        $regimenSimpleTributacion = $taxesInformation['regimenSimpleTributacion'] ?? null;
        $impuestoNacionalConsumo = $taxesInformation['impuestoNacionalConsumo'] ?? null;
        $impuestoSobreVentas = $taxesInformation['impuestoSobreVentas'] ?? null;
        $dvNit = $taxesInformation['dvNit'] ?? null;
        $date = new \DateTime();
        $taxesInformation = new TaxesInformation();
        $customerTaxesInformation->setTaxesTypePerson($taxTypePerson);
        $customerTaxesInformation->setDvNit($dvNit);
        $customerTaxesInformation->setGranContribuyente($granContribuyente);
        $customerTaxesInformation->setAutorretenedor($autorretenedor);
        $customerTaxesInformation->setAgenteDeRetencionIVA($agenteDeRetencionIVA);
        $customerTaxesInformation->setRegimenSimple($regimenSimple);
        $customerTaxesInformation->setImpuestoNacionalConsumo($impuestoNacionalConsumo);
        $customerTaxesInformation->setImpuestoSobreVentasIVA($impuestoSobreVentasIVA);
        $customerTaxesInformation->setUpdatedDate($date);
        return $customerTaxesInformation;
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
