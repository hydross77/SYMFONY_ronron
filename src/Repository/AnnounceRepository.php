<?php

namespace App\Repository;

use App\Entity\Announce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\DBAL\Exception;

/**
 * @extends ServiceEntityRepository<Announce>
 *
 * @method Announce|null find($id, $lockMode = null, $lockVersion = null)
 * @method Announce|null findOneBy(array $criteria, array $orderBy = null)
 * @method Announce[]    findAll()
 * @method Announce[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AnnounceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Announce::class);
    }

    /**
     * Récupère les Announces en lien avec une recherche
     * @param array $parameters
     * @return Query
     */
    public function findSearch(array $parameters): Query
    {
        $qb = $this->createQueryBuilder('a');
        $qb->orderBy('a.dateCat', 'DESC')
            ->leftJoin('a.cat', 'cat')
            ->leftJoin('cat.color', 'color');


        if (!empty($parameters['type'])) {
            $qb->andWhere('a.type = :type')
                ->setParameter('type', $parameters['type']);
        }

        if (!empty($parameters['city'])) {
            $qb->andWhere('a.city = :city')
                ->setParameter('city', $parameters['city']);
        }

        if (!empty($parameters['breed'])) {
            $qb->andWhere('cat.breed = :breed')
                ->setParameter('breed', $parameters['breed']);
        }

        if (!empty($parameters['length_coat'])) {
            $qb->andWhere('cat.lengthCoat = :lengthCoat')
                ->setParameter('lengthCoat', $parameters['length_coat']);
        }

        if (!empty($parameters['design_coat'])) {
            $qb->andWhere('cat.designCoat = :designCoat')
                ->setParameter('designCoat', $parameters['design_coat']);
        }

        if (!empty($parameters['sexe'])) {
            $qb->andWhere('cat.sexe = :sexe')
                ->setParameter('sexe', $parameters['sexe']);
        }

        if (!empty($parameters['color'])) {
            $colors = is_array($parameters['color']) ? $parameters['color'] : [$parameters['color']];
            $qb->andWhere('color.id IN (:colors)')
                ->setParameter('colors', $colors);
        }

        return $qb->getQuery();
    }



    /**
     * @throws Exception
     */
    public function findSearch2(array $parameters): Query
    {
        $qb = $this->createQueryBuilder('a');
        $qb->join('a.cat', 'cat');
        $qb->leftJoin('cat.color', 'color');
        $qb->orderBy('a.dateCat', 'DESC');

        if (!empty($parameters['name'])) {
            $qb->andWhere('cat.name LIKE :name')
                ->setParameter('name', "%{$parameters['name']}%");
        }

        if (!empty($parameters['city'])) {
            $qb->andWhere('a.city = :city')
                ->setParameter('city', $parameters['city']);
        }

        if (!empty($parameters['color'])) {
            $colors = array_map(function($color) {
                return serialize($color);
            }, $parameters['color']);
            $qb->andWhere('color.id IN (:colors)')
                ->setParameter('colors', $colors);
        }

        return $qb->getQuery();
    }




    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function findNotSoldQuery(): \Doctrine\ORM\QueryBuilder
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.type = lost')
            ;
    }


    /**
     * @return Query
     */
    public function findAllNotSoldQuery(): Query
    {
        return $this->findNotSoldQuery()
            ->getQuery()
            ;
    }



    public function save(Announce $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Announce $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Announce[] Returns an array of Announce objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Announce
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
