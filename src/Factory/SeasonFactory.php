<?php

namespace App\Factory;

use App\Entity\Season;

class SeasonFactory
{
    public function createFromTmdbData(array $seasonData): Season
    {
        $season = new Season();
        return $season
            ->setTmdbId($seasonData['id'] ?? '')
            ->setNumber($seasonData['season_number'] ?? 0)
            ->setTitle($seasonData['name'] ?? '')
            ->setPicture($seasonData['poster_path'] ?? '')
            ->setResume($seasonData['overview'] ?? '');
    }
}
