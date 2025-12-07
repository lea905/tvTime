<?php

namespace App\Factory;

use App\Entity\Season;

class SeasonFactory
{
    public function createDataFromTmdbData(array $seasonData): Season
    {
        $season = new Season();
        return $season
            ->setTmdbId($seasonData['id'] ?? '')
            ->setNumber($seasonData['season_number'] ?? 0)
            ->setTitle($seasonData['name'] ?? '')
            ->setPicture($seasonData['poster_path'] ?? '')
            ->setResume($seasonData['overview'] ?? '')
            ->setNumberEpisodes(isset($seasonData['episode_count']) ? $seasonData['episode_count'] : 0);
    }
}
