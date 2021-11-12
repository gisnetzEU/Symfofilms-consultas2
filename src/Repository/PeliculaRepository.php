<?php

namespace App\Repository;

use App\Entity\Pelicula;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Pelicula|null find($id, $lockMode = null, $lockVersion = null)
 * @method Pelicula|null findOneBy(array $criteria, array $orderBy = null)
 * @method Pelicula[]    findAll()
 * @method Pelicula[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PeliculaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Pelicula::class);
    }

    public function findAllByDuration(int $min, int $max):array {

        //consulta en DQL (Doctrine Query Language)
        return $this->getEntityManager()->createQuery(
            "SELECT p
             FROM App\Entity\Pelicula p
             WHERE p.duracion BETWEEN :min AND :max
             ORDER BY p.duracion DESC"
        )
        ->setParameter("min", $min)
        ->setParameter("max", $max)
        ->getResult();
    }

    // /**
    //  * @return Pelicula[] Returns an array of Pelicula objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Pelicula
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
