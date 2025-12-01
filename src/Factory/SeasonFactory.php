<?php

namespace App\Factory;

use App\Entity\Season;

class SeasonFactory
{
    public function __construct(
        private EpisodeFactory $episodeFactory,
    )
    {
    }

    public function createFromTmdbData(array $seasonData): Season
    {
        $season = new Season();

        $season
            ->setTmdbId($seasonData['id'] ?? '')
            ->setNumber($seasonData['season_number'] ?? 0)
            ->setTitle($seasonData['name'] ?? '')
            ->setPicture($seasonData['poster_path'] ?? '')
            ->setResume($seasonData['overview'] ?? '');

        // Episodes (OneToMany)
        if (!empty($seasonData['episodes'])) {
            foreach ($seasonData['episodes'] as $episodeData) {
                $episode = $this->episodeFactory->createFromTmdbData($episodeData, $season);
                $season->addEpisode($episode);
            }
        }

        return $season;
    }
}
