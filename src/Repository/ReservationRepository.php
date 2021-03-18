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


    public function getPaginatedResPerClient($db,$page, $limit,$filters=null){
        $query = $this->createQueryBuilder('a')
            ->join('a.hotel','h')
            ->addSelect('h')
            ->join('a.client','cl')
            ->addSelect('cl')
            ->where('cl.id=:id')
            ->andWhere('a.confirme like :conf ')
            ->setParameter('conf','confirme')

            ->setParameter('id',$db);

        if($filters != null){
            $query->andWhere('h.nom like :cats')
                ->setParameter('cats','%'.$filters.'%');
        }

        $query ->setFirstResult(($page * $limit) - $limit)
            ->setMaxResults($limit)
        ;
        return $query->getQuery()->getResult();
    }


    public function getTotalResPerClient($db,$filters=null){
        $query = $this->createQueryBuilder('a')
            ->join('a.hotel','h')
            ->addSelect('h')
            ->join('a.client','cl')
            ->addSelect('cl')
            ->where('cl.id=:id')
            ->andWhere('a.confirme like :conf ')

            ->setParameter('id',$db)
             ->setParameter('conf','confirme');

        if($filters != null){
            $query->andWhere('h.nom like :cats')
                ->setParameter('cats','%'.$filters.'%');
        }


        return $query->getQuery()->getResult();
    }

////////////////////// INDEX DE RESERVATION DU PAR CLIENT




    public function getPaginatedRes($page, $limit,$filters){
        $query = $this->createQueryBuilder('a')
            ->join('a.hotel','c')
            ->addSelect('c')
            ->andWhere('a.confirme like :conf ')
            ->setParameter('conf','confirme');
        if($filters != null){
            $query->andWhere('c.nom like :cats')
                ->setParameter('cats','%'.$filters.'%');
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
    public function getTotalReservation($filters=null){
        $query = $this->createQueryBuilder('a')
            ->join('a.hotel','c')
            ->addSelect('c')
        ->andWhere('a.confirme like :conf ')
            ->setParameter('conf','confirme');
        if($filters != null){
            $query->andWhere('c.nom like :cats')
                ->setParameter('cats','%'.$filters.'%');
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
