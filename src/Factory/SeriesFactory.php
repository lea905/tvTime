<?php

namespace App\Factory;

use App\Entity\Series;
use App\Factory\CreatorFactory;
use App\Factory\ProductionCompanieFactory;
use App\Factory\SeasonFactory;
use App\Utils\TmdbGenres;

class SeriesFactory
{
    public function __construct(
        private CreatorFactory            $creatorFactory,
        private ProductionCompanieFactory $productionCompanieFactory,
        private SeasonFactory             $seasonFactory
    )
    {
    }

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

        // Creator (ManyToMany)
        if (!empty($serie['created_by'])) {
            foreach ($serie['created_by'] as $creatorData) {
                $creator = $this->creatorFactory->createFromTmdbData($creatorData);
                $serieToReturn->addCreator($creator);
            }
        }

        // Production companies (ManyToMany)
        if (!empty($serie['production_companies'])) {
            foreach ($serie['production_companies'] as $pcData) {
                $pc = $this->productionCompanieFactory->createFromTmdbData($pcData);
                $serieToReturn->addProductionCompany($pc);
            }
        }

        // Seasons (OneToMany)
        if (!empty($serie['seasons'])) {
            foreach ($serie['seasons'] as $seasonData) {
                $season = $this->seasonFactory->createFromTmdbData($seasonData);
                $season->setSeriesId($serieToReturn);
                $serieToReturn->addSeason($season);
            }
        }

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
