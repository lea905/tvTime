<?php
namespace App\Factory;

use App\Entity\Episode;
use App\Entity\Season;

class EpisodeFactory
{

    public function createDataFromTmdbData(array $episodeData, Season $season): Episode
    {
        $episode = new Episode();
        return $episode
            ->setTmdbId($episodeData['id'] ?? '')
            ->setNumber($episodeData['episode_number'] ?? 0)
            ->setTitle($episodeData['name'] ?? '')
            ->setReleaseDate(isset($episodeData['air_date']) ? new \DateTime($episodeData['air_date']) : null)
            ->setResume($episodeData['overview'] ?? '')
            ->setSeason($season);
    }
}
