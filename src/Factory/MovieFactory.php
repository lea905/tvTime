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
            ->setPopularity($movie['popularity'] ?? 0)
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

        // TODO : Gerer les production_companies
        // $movie['production_companies'] contient ce type d'infos :
        // [
        //    0 => array:4 [
        //      "id" => 122699
        //      "logo_path" => null
        //      "name" => "iQIYI Pictures"
        //      "origin_country" => "CN"
        //    ]
        //    1 => array:4 [
        //      "id" => 197030
        //      "logo_path" => "/z9xtT1e3HunOnZUm3uGYb59eL7v.png"
        //      "name" => "Tao Piao Piao"
        //      "origin_country" => "CN"
        //    ]
        //    2 => array:4 [...]
        //    3 => array:4 [...]

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
