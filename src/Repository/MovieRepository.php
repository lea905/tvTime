<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Utils\TmdbGenres;

/**
 * @extends ServiceEntityRepository<Movie>
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Movie::class);
    }

    /**
     * Trouve le film souhaité
     *
     * @param int $id
     * @return Movie|null
     */
    public function findOneById(int $id): ?Movie
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByGenre(string $genre): array
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.genres LIKE :genre')
            ->setParameter('genre', '%"'.$genre.'"%')
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
        $end   = new \DateTime("$year-12-31");

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


    public function add(Movie $movie): bool
    {
        $this->getEntityManager()->persist($movie);
        $this->getEntityManager()->flush();
        return true;
    }

    public function findNowPlaying(Movie $movie)
    {}
}
