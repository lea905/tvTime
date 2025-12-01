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

//    public function createMultipleFromTmdbData(array $data): array
//    {
//        $movies = [];
//
//        foreach ($data['results'] as $movieData) {
//            $movies[] = $this->createFromTmdbData($movieData);
//        }
//
//        return $movies;
//    }
//
//    public function createFromTmdbData(array $movieData): Movie
//    {
//        $movie = new Movie();
//
//        // Conversion des id genres en noms lisibles
//        $genreNames = [];
//        foreach ($movieData['genre_ids'] as $id) {
//            $genreNames[] = TmdbGenres::getName($id);
//        }
//
//        return $movie
//            ->setTitle($movieData['title'] ?? '')
//            ->setResume($movieData['overview'] ?? '')
//            ->setPopularity($movieData['popularity'] ?? 0)
//            ->setPicture($movieData['poster_path'] ?? '')
//            ->setReleaseDate(isset($movieData['release_date']) ? new \DateTime($movieData['release_date']) : null)
//            ->setGenres($genreNames)
//            ->setTmdbId($movieData['id'] ?? '');
//    }

    public function createMultipleFromTmdbData(array $data): array
    {
        $movies = [];

        foreach ($data['results'] as $movieData) {

            // on cherche si le film existe déjà en BDD
            $existingMovie = $this->movieRepository->findOneBy(['tmdbId' => $movieData['id']]);

            if ($existingMovie) {
                // mise à jour de l'entité existante
                $movie = $this->updateFromTmdbData($existingMovie, $movieData);
            } else {
                // création d'une nouvelle entité Movie
                $movie = $this->createFromOneTmdbData($movieData);
            }

            // on persiste/met à jour
            $this->movieRepository->add($movie);

            $movies[] = $movie;
        }

        return $movies;
    }

    public function updateFromTmdbData(Movie $movie, array $movieData): Movie
    {
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

//    public function createFromTmdbData(array $movieData): Movie
//    {
//        dd($movieData);
//        //  array:14 [▼
//        //  "adult" => false
//        //  "backdrop_path" => "/5h2EsPKNDdB3MAtOk9MB9Ycg9Rz.jpg"
//        //  "genre_ids" => array:5 [▶]
//        //  "id" => 1084242
//        //  "original_language" => "en"
//        //  "original_title" => "Zootopia 2"
//        //  "overview" => "After cracking the biggest case in Zootopia's history, rookie cops Judy Hopps and Nick Wilde find themselves on the twisting trail of a great mystery when Gary  ▶"
//        //  "popularity" => 601.095
//        //  "poster_path" => "/oJ7g2CifqpStmoYQyaLQgEU32qO.jpg"
//        //  "release_date" => "2025-11-26"
//        //  "title" => "Zootopia 2"
//        //  "video" => false
//        //  "vote_average" => 7.634
//        //  "vote_count" => 216
//        //]
//        $movie = new Movie();
//
//        $movie->setTitle($movieData['title'] ?? '');
//        $movie->setOverview($movieData['overview'] ?? '');
//        $movie->setPosterPath($movieData['poster_path'] ?? null);
//        $movie->setReleaseDate(isset($movieData['release_date']) ? new \DateTime($movieData['release_date']) : null);
//        $movie->setTmdbId($movieData['id']);
//
//        return $movie;
//    }


    public function createFromOneTmdbData(array $movie): Movie
    {
        $movieToReturn = new Movie();

        // Conversion des id genres en noms lisibles
        $genreNames = [];
//        foreach ($movie['genre_ids'] as $genre) {
//            $genreNames[] = $genre['name'];
//        }

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
