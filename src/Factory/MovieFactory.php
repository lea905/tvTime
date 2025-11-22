<?php

namespace App\Factory;

use App\Entity\Movie;
use App\Utils\TmdbGenres;

class MovieFactory
{

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
            ->setPicture($movieData['poster_path'] ?? '')
            ->setReleaseDate(isset($movieData['release_date']) ? new \DateTime($movieData['release_date']) : null)
            ->setGenres($genreNames)
            ->setTmdbId($movieData['id'] ?? '');
    }
}
