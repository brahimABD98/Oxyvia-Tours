<?php

namespace App\Repository;

use App\Entity\Depense;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Depense|null find($id, $lockMode = null, $lockVersion = null)
 * @method Depense|null findOneBy(array $criteria, array $orderBy = null)
 * @method Depense[]    findAll()
 * @method Depense[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DepenseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Depense::class);
    }

    // /**
    //  * @return Depense[] Returns an array of Depense objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Depense
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function OrderById()
    { $em=$this->getEntityManager();
        $query=$em->createQuery('select d from App\Entity\Depense d order by d.date_depense ASC');
        return $query->getResult();
    }

    function findEnabled()
    { return $this->createQueryBuilder('d')
        ->where('d.enabled=:enabled')
        ->andWhere('d.date_depense >= CURRENT_DATE()')
        ->setParameter('enabled',true)
        ->getQuery()->getResult();}

    function findEnabled2()
    { return $this->createQueryBuilder('d')
        ->where('d.enabled=:enabled')
        ->andWhere('d.date_depense <= CURRENT_DATE()')
        ->setParameter('enabled',false)
        ->getQuery()->getResult();}

    function SearchID($prenom,$nom,$id,$occupation,$id_personnel,$salaire,$horaire_reguliere,$horaire_sup,$exempte,$date_depense){
        return $this->createQueryBuilder('d')
            ->where('d.prenom LIKE :prenom')
            ->orWhere('d.nom LIKE :nom')
            ->orWhere('d.id LIKE :id')
            ->orWhere('d.occupation LIKE :occupation')
            ->orWhere('IDENTITY(d.id_personnel) LIKE :id_personnel')
            ->orWhere('d.salaire LIKE :salaire')
            ->orWhere('d.horaire_reguliere LIKE :horaire_reguliere')
            ->orWhere('d.horaire_sup LIKE :horaire_sup')
            ->orWhere('d.exempte LIKE :exempte')
            ->orWhere('d.date_depense LIKE :date_depense')
            ->setParameter('prenom','%'.$prenom.'%')
            ->setParameter('nom','%'.$nom.'%')
            ->setParameter('id','%'.$id.'%')
            ->setParameter('occupation','%'.$occupation.'%')
            ->setParameter('id_personnel','%'.$id_personnel.'%')
            ->setParameter('salaire','%'.$salaire.'%')
            ->SetParameter('horaire_reguliere','%'.$horaire_reguliere.'%')
            ->SetParameter('horaire_sup','%'.$horaire_sup.'%')
            ->SetParameter('exempte','%'.$exempte.'%')
            ->SetParameter('date_depense','%'.$date_depense.'%')





            ->getQuery()->getResult();
    }
    public function findStudentById($id)
    {  return $this->createQueryBuilder('depense')
    ->where('depense.id LIKE :id')
    ->setParameter('id','%'.$id.'%')
    ->getQuery()
    ->getResult();}

    /**
     * @return void
     */
    public function countByOccupation()
    {$query = $this->createQueryBuilder('d')
        ->select('d.occupation as occupations , COUNT(d) as count')
        ->groupBy('occupations')
    ;
        return $query->getQuery()->getResult();
    }





}
