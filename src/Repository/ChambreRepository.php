<?php

namespace App\Repository;

use App\Entity\Chambre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Chambre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Chambre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Chambre[]    findAll()
 * @method Chambre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ChambreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Chambre::class);
    }

    public function NbChambreSingleDispo($db){
        return $this->createQueryBuilder('s')
            ->select('count(s.id),s')
            ->where(' IDENTITY(s.hotel) like :db')
            ->andWhere('s.occupe like :occ')
            ->andWhere('s.type like :typeroom')
            ->setParameter('db',$db)
            ->setParameter('occ','non occupe')
            ->setParameter('typeroom','single room')
            ->getQuery()
            ->getResult();

    }

    public function getChambresDoubleWithLimit($db,$nb){
        return $this->createQueryBuilder('s')
            ->select('s')
            ->setMaxResults($nb)
            ->where(' IDENTITY(s.hotel) like :db')
            ->andWhere('s.occupe like :occ')
            ->andWhere('s.type like :typeroom')
            ->setParameter('db',$db)
            ->setParameter('occ','non occupe')
            ->setParameter('typeroom','double room')
            ->getQuery()
            ->getResult();

    }

    public function getChambresSingleWithLimit($db,$nb){
        return $this->createQueryBuilder('s')
            ->select('s')
            ->setMaxResults($nb)
            ->where(' IDENTITY(s.hotel) like :db')
            ->andWhere('s.occupe like :occ')
            ->andWhere('s.type like :typeroom')
            ->setParameter('db',$db)
            ->setParameter('occ','non occupe')
            ->setParameter('typeroom','single room')
            ->getQuery()
            ->getResult();

    }



    public function NbChambreDoubleDispo($db){

        return $this->createQueryBuilder('s')
            ->select('count(s.id),s')
            ->where(' IDENTITY(s.hotel) like :db')
            ->andWhere('s.occupe like :occ')
            ->andWhere('s.type like :typeroom')
            ->setParameter('db',$db)
            ->setParameter('occ','non occupe')
            ->setParameter('typeroom','double room')
            ->getQuery()
            ->getResult();

    }



    public function showChambreExpire(){
        return $this->createQueryBuilder('s')
            ->join('s.reservation','c')
            ->addSelect('c')
            ->where('c.date_fin<:today')
            ->andWhere('s.occupe like :occ')

            ->setParameter('today',new \DateTime())
            ->setParameter('occ','occupe')

            ->getQuery()
            ->getResult();

        //  $em=$this->getEntityManager();
        //$query=$em->createQuery(
        //  "select c from App\Entity\Classroom c "
        //);
        //return $query->getResult();
    }



    // /**
    //  * @return Chambre[] Returns an array of Chambre objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Chambre
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
