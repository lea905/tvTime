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
}
