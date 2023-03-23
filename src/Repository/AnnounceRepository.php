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
     * @return Announce[]
     */
    public function findSearch(array $parameters): Query //parameters est le tableau
    {
        $qb = $this->createQueryBuilder('a');
        // fait une requête sur l'entité 'A' : 'ANNOUNCE'



        if (!empty($parameters['type'])) {
            $qb->andWhere('a.type = :type')
                ->setParameter('type', $parameters['type']);
        }


        if (!empty($parameters['city'])) {
            $qb->andWhere('a.city = :city')
                ->setParameter('city', $parameters['city']);
        }
        //dd($qb->getDQL());
        // requete bdd



        return $qb->getQuery();
        //->getResult(); //retourne le tableau des résultats
    }

    /**
     * @throws Exception
     */
    public function findSearch2(array $parameters): Query
    {
        $qb = $this->createQueryBuilder('a');
        $qb->join('a.cat', 'cat');
        $qb->leftJoin('cat.color', 'color');

        if (!empty($parameters['name'])) {
            $qb->andWhere('cat.name LIKE :name')
                ->setParameter('name', "%{$parameters['name']}%");
        }

        if (!empty($parameters['color'])) {
            $qb->andWhere('color.id LIKE :id')
                ->setParameter('id', "%{$parameters['color']}%");
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
