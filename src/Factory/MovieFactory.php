<?php

namespace App\Factory;

use App\Entity\Movie;
use App\Utils\TmdbGenres;

class MovieFactory
{

    public function __construct(
        private ProductionCompanieFactory $productionCompanieFactory
    )
    {
    }

    public function createMultipleFromTmdbData(array $data): array
    {
        $movies = [];

        foreach ($data['results'] as $movieData) {
            $movies[] = $this->createFromTmdbData($movieData);
        }

        return $movies;
    }

    public function createFromTmdbData(array $movieData): Movie
    {
        $movie = new Movie();

        // Conversion des id genres en noms lisibles
        $genreNames = [];
        foreach ($movieData['genre_ids'] as $id) {
            $genreNames[] = TmdbGenres::getName($id);
        }

        return $movie
            ->setTitle($movieData['title'] ?? '')
            ->setResume($movieData['overview'] ?? '')
            ->setPopularity($movieData['popularity'] ?? 0)
            ->setPicture($movieData['poster_path'] ?? '')
            ->setReleaseDate(isset($movieData['release_date']) ? new \DateTime($movieData['release_date']) : null)
            ->setGenres($genreNames)
            ->setTmdbId($movieData['id'] ?? '');
    }

    public function createFromOneTmdbData(array $movie): Movie
    {
        $movieToReturn = new Movie();

        // Conversion des id genres en noms lisibles
        $genreNames = [];
        foreach ($movie['genres'] as $genre) {
            $genreNames[] = $genre['name'];
        }

        // Production companies (ManyToMany)
        if (!empty($movie['production_companies'])) {
            foreach ($movie['production_companies'] as $pcData) {
                $pc = $this->productionCompanieFactory->createFromTmdbData($pcData);
                $movieToReturn->addProductionCompany($pc);
            }
        }

        return $movieToReturn
            ->setTitle($movie['title'] ?? '')
            ->setStatus($movie['status'] ?? '')
            ->setPopularity($movie['popularity'] ?? 0)
            ->setResume($movie['overview'] ?? '')
            ->setPicture($movie['poster_path'] ?? '')
            ->setReleaseDate(isset($movie['release_date']) ? new \DateTime($movie['release_date']) : null)
            ->setGenres($genreNames)
            ->setTmdbId($movie['id'] ?? '');
    }
}
