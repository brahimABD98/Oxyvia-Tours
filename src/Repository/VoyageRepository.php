<?php

namespace App\Repository;

use App\Entity\Voyage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Voyage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Voyage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Voyage[]    findAll()
 * @method Voyage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VoyageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Voyage::class);
    }


///get list of voy that are not full
    // /**
    //  * @return Voyage[] Returns an array of Voyage objects
    //  */
    public function Voyagelist()
    {
        return $this->createQueryBuilder('u')
            ->where('u.nb_personne <> :val')
            ->setParameter('val','0')
            ->getQuery()
            ->getResult();
    }


    public function dateDebutGroupedBy()
    {
        return $this->createQueryBuilder('u')
            ->select("date_format(u.date_debut, '%Y-%m-%d') as wiw" )
            ->groupBy('wiw')
            ->getQuery()
            ->getResult();
    }

    public function datefinGroupedBy()
    {
        return $this->createQueryBuilder('u')
            ->select("date_format(u.date_fin, '%Y-%m-%d') as wiw" )
            ->groupBy('wiw')
            ->getQuery()
            ->getResult();
    }

    public function VilleGroupedBy()
    {
        return $this->createQueryBuilder('u')

            ->groupBy('u.ville')
            ->getQuery()
            ->getResult();
    }
    public function getPaginatedvoyage($page, $limit,$filterville=null,$filtersdb=null,$filtersdf=null){
        $query = $this->createQueryBuilder('c');

        if($filtersdb != null&&$filtersdf != null&&$filterville != null){
            $query->andWhere('c.date_debut like :ville')
                ->andWhere('c.date_fin like :db')
                ->andWhere('c.ville like :v')
                ->setParameter('ville','%'.$filtersdb.'%')
                ->setParameter('db','%'.$filtersdf.'%')
                  ->setParameter('v','%'.$filterville.'%');

        }


       else if($filterville != null&&$filtersdb != null){
            $query->andWhere('c.ville like :ville')
                ->andWhere('c.date_debut like :db')
                ->setParameter('ville','%'.$filterville.'%')
                ->setParameter('db','%'.$filtersdb.'%');

        }

       else if($filterville != null&&$filtersdf != null){
            $query->andWhere('c.ville like :ville')
                ->andWhere('c.date_fin like :db')
                ->setParameter('ville','%'.$filterville.'%')
                ->setParameter('db','%'.$filtersdf.'%');

        }

       else if($filtersdb != null&&$filtersdf != null){
            $query->andWhere('c.date_debut like :ville')
                ->andWhere('c.date_fin like :db')
                ->setParameter('ville','%'.$filtersdb.'%')
                ->setParameter('db','%'.$filtersdf.'%');

        }
       else if($filterville != null){
            $query->andWhere('c.ville like :ville')
                ->setParameter('ville','%'.$filterville.'%');
        }
       else  if($filtersdb != null){
            $query->andWhere('c.date_debut like :db')
                ->setParameter('db','%'.$filtersdb.'%');
        }
       else  if($filtersdf != null){
            $query->andWhere('c.date_fin like :df')
                ->setParameter('df','%'.$filtersdf.'%');
        }



        $query ->setFirstResult(($page * $limit) - $limit)
            ->setMaxResults($limit)
        ;
        return $query->getQuery()->getResult();
    }


    /**
     * Returns number of Annonces
     * @return void
     */
    public function getTotalVoy($filterville=null,$filtersdb=null,$filtersdf=null)
    {
        $query = $this->createQueryBuilder('c');

        if ($filtersdb != null && $filtersdf != null && $filterville != null) {
            $query->andWhere('c.date_debut like :ville')
                ->andWhere('c.date_fin like :db')
                ->andWhere('c.ville like :v')
                ->setParameter('ville', '%' . $filtersdb . '%')
                ->setParameter('db', '%' . $filtersdf . '%')
                ->setParameter('v', '%' . $filterville . '%');

        } else if ($filterville != null && $filtersdb != null) {
            $query->andWhere('c.ville like :ville')
                ->andWhere('c.date_debut like :db')
                ->setParameter('ville', '%' . $filterville . '%')
                ->setParameter('db', '%' . $filtersdb . '%');

        } else if ($filterville != null && $filtersdf != null) {
            $query->andWhere('c.ville like :ville')
                ->andWhere('c.date_fin like :db')
                ->setParameter('ville', '%' . $filterville . '%')
                ->setParameter('db', '%' . $filtersdf . '%');

        } else if ($filtersdb != null && $filtersdf != null) {
            $query->andWhere('c.date_debut like :ville')
                ->andWhere('c.date_fin like :db')
                ->setParameter('ville', '%' . $filtersdb . '%')
                ->setParameter('db', '%' . $filtersdf . '%');

        } else if ($filterville != null) {
            $query->andWhere('c.ville like :ville')
                ->setParameter('ville', '%' . $filterville . '%');
        } else if ($filtersdb != null) {
            $query->andWhere('c.date_debut like :db')
                ->setParameter('db', '%' . $filtersdb . '%');
        } else if ($filtersdf != null) {
            $query->andWhere('c.date_fin like :df')
                ->setParameter('df', '%' . $filtersdf . '%');
        };
        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return Voyage[] Returns an array of Voyage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Voyage
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
