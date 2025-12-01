<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use Doctrine\Persistence\ManagerRegistry;

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

    /**
     * Trouves les films du genre donné
     *
     * @param string $genre
     * @return array
     */
    public function findByGenre(string $genre): array | null
    {
        if(TmdbGenres::searchGenre($genre) !== null){
            return $this->createQueryBuilder('m')
                ->andWhere('m.genres LIKE :genre')
                ->setParameter('genre', '%' . $genre . '%')
                ->getQuery()
                ->getResult();
        };
        return null;
    }

    public function add(Movie $movie): bool
    {
        $this->getEntityManager()->persist($movie);
        $this->getEntityManager()->flush();
        return true;
    }
}
