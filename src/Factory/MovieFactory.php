<?php

namespace App\Factory;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use App\Utils\TmdbGenres;

class MovieFactory
{

    public function __construct(
        private ProductionCompanieFactory $productionCompanieFactory,
        private readonly MovieRepository  $movieRepository
    )
    {
    }

    public function createMultipleFromTmdbData(array $data): array
    {
        $movies = [];

        foreach ($data['results'] as $movieData) {

            // on cherche si le film existe déjà en BDD
            $existingMovie = $this->movieRepository->findOneBy(['tmdbId' => $movieData['id']]);

            if ($existingMovie) {
                // mise à jour de l'entité existante
                $movie = $this->setDataFromTmdbData($movieData, $existingMovie);
            } else {
                // création d'une nouvelle entité Movie
                $movie = $this->setDataFromTmdbData($movieData);
            }

            // on persiste/met à jour
            $this->movieRepository->add($movie);

            $movies[] = $movie;
        }

        return $movies;
    }

    public function setDataFromTmdbData(array $movieData, Movie $movie = null): Movie
    {
        // Si le film n'est pas dans la BDD
        if($movie == null) {
            $movie = new Movie();
        }

        // Conversion des id genres en noms lisibles
        $genreNames = [];
        foreach ($movieData['genre_ids'] as $id) {
            $genreNames[] = TmdbGenres::getName($id);
        }

        // Production companies (ManyToMany)
        if (!empty($movieData['production_companies'])) {
            foreach ($movieData['production_companies'] as $pcData) {
                $pc = $this->productionCompanieFactory->createFromTmdbData($pcData);
                $movie->addProductionCompany($pc);
            }
        }

        return $movie
            ->setTitle($movieData['title'] ?? '')
            ->setStatus($movieData['status'] ?? '')
            ->setPopularity($movieData['popularity'] ?? 0)
            ->setResume($movieData['overview'] ?? '')
            ->setPicture($movieData['poster_path'] ?? '')
            ->setReleaseDate(isset($movieData['release_date']) ? new \DateTime($movieData['release_date']) : null)
            ->setGenres($genreNames)
            ->setTmdbId($movieData['id'] ?? '');
    }
}
