<?php

namespace App\Factory;

use App\Entity\Series;
use App\Utils\TmdbGenres;

class SeriesFactory
{
    public function createMultipleFromTmdbData(array $data): array
    {
        $series = [];

        foreach ($data['results'] as $seriesData) {
            $series[] = $this->createFromTmdbData($seriesData);
        }

        return $series;
    }

    public function createFromTmdbData(array $seriesData): Series
    {
        $series = new Series();

        // Conversion des id genres en noms lisibles
        $genreNames = [];
        foreach ($seriesData['genre_ids'] as $id) {
            $genreNames[] = TmdbGenres::getName($id);
        }

        return $series
            ->setTitle($seriesData['name'] ?? '')
            ->setResume($seriesData['overview'] ?? '')
            ->setPicture($seriesData['poster_path'] ?? '')
            ->setReleaseDate(isset($seriesData['first_air_date']) ? new \DateTime($seriesData['first_air_date']) : null)
            ->setGenres($genreNames)
            ->setTmdbId($seriesData['id'] ?? '');
    }

    public function createFromOneTmdbData(array $serie): Series
    {
        $serieToReturn = new Series();

        // Conversion des id genres en noms lisibles
        $genreNames = [];
        foreach ($serie['genres'] as $genre) {
            $genreNames[] = $genre['name'];
        }

        // TODO : series_creator
        // $serie['created_by'] contient ce type d'infos :
        // "created_by" => array:2 [▼
        //    0 => array:6 [▼
        //      "id" => 1179422
        //      "credit_id" => "57599b039251410a99001cce"
        //      "name" => "Ross Duffer"
        //      "original_name" => "Ross Duffer"
        //      "gender" => 2
        //      "profile_path" => "/kN1HdFViQkcJOQlNcvvFJIx9Uju.jpg"
        //    ]
        //    1 => array:6 [▼
        //      "id" => 1179419
        //      "credit_id" => "57599b0e925141378a002c87"
        //      "name" => "Matt Duffer"
        //      "original_name" => "Matt Duffer"
        //      "gender" => 2
        //      "profile_path" => "/kXO5CnSxC0znMAICGxnPeuGP73U.jpg"
        //    ]
        //  ]

        // TODO : Gerer les production_companies
        // $serie['production_companies'] contient ce type d'infos :
        // "production_companies" => array:3 [▼
        //    0 => array:4 [▼
        //      "id" => 2575
        //      "logo_path" => "/9YJrHYlcfHtwtulkFMAies3aFEl.png"
        //      "name" => "21 Laps Entertainment"
        //      "origin_country" => "US"
        //    ]
        //    1 => array:4 [▶]
        //    2 => array:4 [▶]
        //  ]

        // TODO : Gerer les seasons
        // $serie['seasons'] contient ce type d'infos :
        //   "seasons" => array:5 [▼
        //    0 => array:8 [▼
        //      "air_date" => "2016-07-15"
        //      "episode_count" => 8
        //      "id" => 77680
        //      "name" => "Season 1"
        //      "overview" => "Strange things are afoot in Hawkins, Indiana, where a young boy's sudden disappearance unearths a young girl with otherworldly powers."
        //      "poster_path" => "/rbnuP7hlynAMLdqcQRCpZW9qDkV.jpg"
        //      "season_number" => 1
        //      "vote_average" => 8.4
        //    ]
        //    1 => array:8 [▶]
        //    2 => array:8 [▶]
        //    3 => array:8 [▶]
        //    4 => array:8 [▶]
        //  ]

        return $serieToReturn
            ->setTitle($serie['name'] ?? '')
            ->setStatus($serie['status'] ?? '')
            ->setTmdbId($serie['id'] ?? '')
            ->setPopularity($serie['popularity'] ?? '')
            ->setNumberEpisodes($serie['number_of_episodes'] ?? 0)
            ->setNumberSeasons($serie['number_of_seasons'] ?? 0)
            ->setPicture($serie['poster_path'] ?? '')
            ->setResume($serie['overview'] ?? '')
            ->setReleaseDate(isset($serie['first_air_date']) ? new \DateTime($serie['first_air_date']) : null);
    }
}
