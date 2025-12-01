<?php

namespace App\Repository;

use App\Entity\Series;
use App\Utils\TmdbGenres;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Series>
 */
class SeriesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Series::class);
    }

    /**
     * Trouve la série souhaitée
     *
     * @param int $id
     * @return Series|null
     */
    public function findOneById(int $id): ?Series
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Trouves les séries du genre donné
     *
     * @param string $genre
     * @return array
     */
//    public function findByGenre(string $genre): array | null
//    {
//        if(TmdbGenres::searchGenre($genre) !== null){
//            return $this->createQueryBuilder('m')
//                ->andWhere('m.genres LIKE :genre')
//                ->setParameter('genre', '%' . $genre . '%')
//                ->getQuery()
//                ->getResult();
//        };
//        return null;
//    }
}
