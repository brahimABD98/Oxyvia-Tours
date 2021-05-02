<?php

namespace App\Repository;

use App\Entity\Reservation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function TotalPrixPerMonth(){
   //     return $this->createQueryBuilder('s')

//            ->join('s.client','c')
//            ->addSelect('c')
//            ->where('c.id=:id')
//            ->setParameter('conf','confirme')
//
//            ->getQuery()
//            ->getResult();

          $em=$this->getEntityManager();
        $query=$em->createQuery(
          "select sum(c.prix) as prix ,MONTH(c.date_debut) as mois from App\Entity\Reservation c group by mois "
        );
        return $query->getResult();
    }



    public function showReservationParClient($db){
        return $this->createQueryBuilder('s')

            ->join('s.client','c')
            ->addSelect('c')
            ->where('c.id=:id')
            ->setParameter('id',$db)
            ->setParameter('conf','confirme')

            ->getQuery()
            ->getResult();

        //  $em=$this->getEntityManager();
        //$query=$em->createQuery(
        //  "select c from App\Entity\Classroom c "
        //);
        //return $query->getResult();
    }


    public function getPaginatedResPerClient($idclient,$page, $limit,$filters=null,$filterType=null){
        $query = $this->createQueryBuilder('a')
            ->join('a.hotel','h')
            ->addSelect('h')
            ->join('a.client','cl')
            ->addSelect('cl')
            ->where('cl.id=:id')
            ->andWhere('a.confirme like :conf ')
            ->setParameter('conf','confirme')
            ->setParameter('id',$idclient);

        if($filters != null&&$filterType != null){
            $query->andWhere('a.type like :typeRes')
                ->setParameter('typeRes','%'.$filterType.'%');

                    if($filterType=='reservation voyage'){
                        $query   ->join('a.voyage','vo')
                            ->addSelect('vo')
                             ->andWhere('vo.nom like :cats')
                               ->setParameter('cats','%'.$filters.'%');
                    }
                    else  if($filterType=='reservation hotel'){
                        $query ->andWhere('h.nom like :cats')
                            ->setParameter('cats','%'.$filters.'%');
                    }
        }
        else if($filterType != null){

            $query->andWhere('a.type like :typeRes')
                ->setParameter('typeRes','%'.$filterType.'%');

        }
        else if($filters != null){
            $query->andWhere('h.nom like :cats')
                ->setParameter('cats','%'.$filters.'%');
        }



        $query ->setFirstResult(($page * $limit) - $limit)
            ->setMaxResults($limit)
        ;
        return $query->getQuery()->getResult();
    }


    public function getTotalResPerClient($idclient,$filters=null,$filterType=null){
        $query = $this->createQueryBuilder('a')
            ->join('a.hotel','h')
            ->addSelect('h')
            ->join('a.client','cl')
            ->addSelect('cl')
            ->where('cl.id=:id')
            ->andWhere('a.confirme like :conf ')
            ->setParameter('conf','confirme')
            ->setParameter('id',$idclient);


        if($filters != null&&$filterType != null){
            $query->andWhere('a.type like :typeRes')
                ->setParameter('typeRes','%'.$filterType.'%');

            if($filterType=='reservation voyage'){
                $query   ->join('a.voyage','vo')
                    ->addSelect('vo')
                    ->andWhere('vo.nom like :cats')
                    ->setParameter('cats','%'.$filters.'%');
            }
            else  if($filterType=='reservation hotel'){
                $query ->andWhere('h.nom like :cats')
                    ->setParameter('cats','%'.$filters.'%');
            }



        }

        else if($filterType != null){

            $query->andWhere('a.type like :typeRes')
                ->setParameter('typeRes','%'.$filterType.'%');

        }
        else if($filters != null){
            $query->andWhere('h.nom like :cats')
                ->setParameter('cats','%'.$filters.'%');
        }



        return $query->getQuery()->getResult();
    }

////////////////////// INDEX DE RESERVATION DU PAR CLIENT UP taa client eli louta taa all




    public function getPaginatedRes($page, $limit,$filters,$filterType=null){
        $query = $this->createQueryBuilder('a')
            ->join('a.hotel','h')
            ->addSelect('h')
            ->andWhere('a.confirme like :conf ')
            ->setParameter('conf','confirme');


        if($filters != null&&$filterType != null){
            $query->andWhere('a.type like :typeRes')
                ->setParameter('typeRes','%'.$filterType.'%');

            if($filterType=='reservation voyage'){
                $query   ->join('a.voyage','vo')
                    ->addSelect('vo')
                    ->andWhere('vo.nom like :cats')
                    ->setParameter('cats','%'.$filters.'%');
            }
            else  if($filterType=='reservation hotel'){
                $query ->andWhere('h.nom like :cats')
                    ->setParameter('cats','%'.$filters.'%');
            }
        }
        else if($filterType != null){

            $query->andWhere('a.type like :typeRes')
                ->setParameter('typeRes','%'.$filterType.'%');

        }
        else if($filters != null){
            $query->andwhere('h.nom like :cats')

                ->setParameter('cats','%'.$filters.'%')
;
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
    public function getTotalReservation($filters=null,$filterType=null){
        $query = $this->createQueryBuilder('a')
            ->join('a.hotel','h')
            ->addSelect('h')
            ->andWhere('a.confirme like :conf ')
            ->setParameter('conf','confirme');

        if($filters != null&&$filterType != null){
            $query->andWhere('a.type like :typeRes')
                ->setParameter('typeRes','%'.$filterType.'%');

            if($filterType=='reservation voyage'){
                $query   ->join('a.voyage','vo')
                    ->addSelect('vo')
                    ->andWhere('vo.nom like :cats')
                    ->setParameter('cats','%'.$filters.'%');
            }
            else  if($filterType=='reservation hotel'){
                $query ->andWhere('h.nom like :cats')
                    ->setParameter('cats','%'.$filters.'%');
            }
        }
        else if($filterType != null){

            $query->andWhere('a.type like :typeRes')
                ->setParameter('typeRes','%'.$filterType.'%');

        }
        else if($filters != null){
            $query ->andWhere('h.nom like :cats')
                ->join('a.voyage','vo')
                ->addSelect('vo')
                ->andWhere('vo.nom like :cats2')

                ->setParameter('cats','%'.$filters.'%')
                ->setParameter('cats2','%'.$filters.'%');
        }


        ;
        return $query->getQuery()->getResult();
    }
    // /**
    //  * @return Reservation[] Returns an array of Reservation objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Reservation
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
