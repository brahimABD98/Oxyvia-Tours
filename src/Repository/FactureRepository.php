<?php

namespace App\Repository;

use App\Entity\Facture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Facture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Facture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Facture[]    findAll()
 * @method Facture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FactureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Facture::class);
    }

    // /**
    //  * @return Facture[] Returns an array of Facture objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('f.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Facture
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function OrderById()
    { $em=$this->getEntityManager();
    $query=$em->createQuery('select f from App\Entity\Facture f order by f.identifiant ASC');
    return $query->getResult();

    }
    function SearchID($identifiant,$id,$montant,$date_paiement,$devise,$moyen_paiement,$mode_paiement,$typeCB,$Ncb,$code_securite,$date_expiration,$location,$pays){
        return $this->createQueryBuilder('f')
            ->where('f.identifiant LIKE :identifiant')
            ->orWhere('f.id LIKE :id')
            ->orWhere('f.montant LIKE :montant')
            ->orWhere('f.date_paiement LIKE :date_paiement')
            ->orWhere('f.devise LIKE :devise')
            ->orWhere('f.moyen_paiement LIKE :moyen_paiement')
            ->orWhere('f.mode_paiement LIKE :mode_paiement')
            ->orWhere('f.typeCB LIKE :typeCB')
            ->orWhere('f.Ncb LIKE :Ncb')
            ->orWhere('f.code_securite LIKE :code_securite')
            ->orWhere('f.date_expiration LIKE :date_expiration')
            ->orWhere('f.location LIKE :location')
            ->orWhere('f.pays LIKE :pays')
            ->setParameter('identifiant','%'.$identifiant.'%')
            ->setParameter('id','%'.$id.'%')
            ->setParameter('montant','%'.$montant.'%')
            ->setParameter('date_paiement','%'.$date_paiement.'%')
            ->setParameter('devise','%'.$devise.'%')
            ->setParameter('moyen_paiement','%'.$moyen_paiement.'%')
            ->setParameter('mode_paiement','%'.$mode_paiement.'%')
            ->setParameter('typeCB','%'.$typeCB.'%')
            ->setParameter('Ncb','%'.$Ncb.'%')
            ->setParameter('code_securite','%'.$code_securite.'%')
            ->setParameter('date_expiration','%'.$date_expiration.'%')
            ->setParameter('location','%'.$location.'%')
            ->setParameter('pays','%'.$pays.'%')





            ->getQuery()->getResult();
    }
    function OrderByCurrentDate(){
        return $this->createQueryBuilder('f')
       ->where('f.date_paiement >= CURRENT_DATE()')
            ->getQuery()->getResult();}

    function OrderByCurrentDateI(){
        return $this->createQueryBuilder('f')
            ->where('f.date_paiement <= CURRENT_DATE()')
            ->getQuery()->getResult();}

    function findEnabled()
    { return $this->createQueryBuilder('f')
        ->where('f.enabled=:enabled')
        ->andWhere('f.date_paiement >= CURRENT_DATE()')
        ->setParameter('enabled',true)
        ->getQuery()->getResult();}

    function findEnabled2()
    { return $this->createQueryBuilder('f')
        ->where('f.enabled=:enabled')
        ->andWhere('f.date_paiement <= CURRENT_DATE()')
        ->setParameter('enabled',false)
        ->getQuery()->getResult();}

    /**
     * @return void
     */
        public function countByDate()
        {$query = $this->createQueryBuilder('f')
        ->select('SUBSTRING(f.date_paiement,1,10) as dateFacture, COUNT(f) as count')
        ->groupBy('dateFacture')
        ;
        return $query->getQuery()->getResult();
        }






}
