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
     */
    public function findOneById(int $id): ?Series
    {
        return $this->createQueryBuilder('s')
            // saisons
            ->leftJoin('s.seasons', 'seasons')
            ->addSelect('seasons')

            // companies de production
            ->leftJoin('s.productionCompanies', 'companies')
            ->addSelect('companies')

            // créateurs
            ->leftJoin('s.creators', 'creators')
            ->addSelect('creators')

            // filtre
            ->andWhere('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Trouves les séries du genre donné
     */
    public function findByGenre(string $genre): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.genres LIKE :genre')
            ->setParameter('genre', '%"' . $genre . '"%')
            ->getQuery()
            ->getResult();
    }

    public function findMostPopular(int $limit = 20): array
    {
        return $this->createQueryBuilder('m')
            ->orderBy('m.popularity', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function findByYear(int $year): array
    {
        $start = new \DateTime("$year-01-01");
        $end = new \DateTime("$year-12-31");

        return $this->createQueryBuilder('m')
            ->andWhere('m.releaseDate BETWEEN :start AND :end')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->orderBy('m.releaseDate', 'ASC')
            ->getQuery()
            ->getResult();
    }


    public function findUpcoming(): array
    {
        $today = new \DateTimeImmutable('today');

        return $this->createQueryBuilder('m')
            ->andWhere('m.releaseDate > :today')
            ->setParameter('today', $today)
            ->orderBy('m.releaseDate', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function add(Series $series): bool
    {
        $this->getEntityManager()->persist($series);
        $this->getEntityManager()->flush();
        return true;
    }

    public function searchByTitle(string $query): array
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.title LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('s.popularity', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
