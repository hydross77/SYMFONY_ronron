<?php

namespace App\Repository;

use App\Entity\Announce;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Types\Types;
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
        // Crée une instance du QueryBuilder avec l'alias 'a' pour la table principale
        $qb = $this->createQueryBuilder('a');

        // Trie les résultats par ordre décroissant de la colonne 'dateCat'
        $qb->orderBy('a.dateCat', 'DESC')

            // Effectue une jointure avec la relation 'cat' en utilisant l'alias 'cat'
            // et une jointure avec la relation 'color' en utilisant l'alias 'color'
            ->leftJoin('a.cat', 'cat')
            ->leftJoin('cat.color', 'color');

        // Ajoute une condition pour filtrer par le type spécifié dans les paramètres
        if (!empty($parameters['type'])) {
            $qb->andWhere('a.type = :type')
                ->setParameter('type', $parameters['type']);
        }

        // Ajoute une condition pour filtrer par la ville spécifiée dans les paramètres
        if (!empty($parameters['city'])) {
            $qb->andWhere('a.city = :city')
                ->setParameter('city', $parameters['city']);
        }

        // Ajoute une condition pour filtrer par la race spécifiée dans les paramètres
        if (!empty($parameters['breed'])) {
            $qb->andWhere('cat.breed = :breed')
                ->setParameter('breed', $parameters['breed']);
        }

        // Ajoute une condition pour filtrer par la longueur du pelage spécifiée dans les paramètres
        if (!empty($parameters['length_coat'])) {
            $qb->andWhere('cat.lengthCoat = :lengthCoat')
                ->setParameter('lengthCoat', $parameters['length_coat']);
        }

        // Ajoute une condition pour filtrer par le motif du pelage spécifié dans les paramètres
        if (!empty($parameters['design_coat'])) {
            $qb->andWhere('cat.designCoat = :designCoat')
                ->setParameter('designCoat', $parameters['design_coat']);
        }

        // Ajoute une condition pour filtrer par la date de la catégorie spécifiée dans les paramètres
        if ($parameters['date_cat'] !== null) {
            $qb->andWhere($qb->expr()->gte('a.dateCat', ':date_cat'))
                ->setParameter('date_cat', $parameters['date_cat']);
        }

        // Ajoute une condition pour filtrer par le sexe du chat spécifié dans les paramètres
        if (!empty($parameters['sexe'])) {
            $qb->andWhere('cat.sexe = :sexe')
                ->setParameter('sexe', $parameters['sexe']);
        }

        // Ajoute les conditions de filtrage pour chaque couleur spécifiée dans les paramètres
        if (!empty($parameters['color'])) {
            foreach ($parameters['color'] as $index => $color) {
                // Effectue une jointure avec la relation 'color' en utilisant un alias dynamique 'color$index'
                $qb->leftJoin("cat.color", "color$index")
                    // Ajoute une condition pour filtrer par l'ID de couleur spécifiée
                    ->andWhere("color$index.id = :color$index")
                    ->setParameter("color$index", $color);
            }
        }

        // Retourne la requête
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
}
