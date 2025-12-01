<?php

namespace App\Factory;

use App\Entity\Series;
use App\Factory\CreatorFactory;
use App\Factory\ProductionCompanieFactory;
use App\Factory\SeasonFactory;
use App\Repository\SeriesRepository;
use App\Utils\TmdbGenres;

class SeriesFactory
{
    public function __construct(
        private CreatorFactory            $creatorFactory,
        private ProductionCompanieFactory $productionCompanieFactory,
        private SeasonFactory             $seasonFactory,
        private SeriesRepository $seriesRepository
    )
    {
    }

    public function createMultipleFromTmdbData(array $data): array
    {
        $series = [];

        foreach ($data['results'] as $serieData) {

            // on cherche si le film existe déjà en BDD
            $existingSeries = $this->seriesRepository->findOneBy(['tmdbId' => $serieData['id']]);

            if ($existingSeries) {
                // mise à jour de l'entité existante
                $serie = $this->setDataFromTmdbData($serieData, $existingSeries);
            } else {
                // création d'une nouvelle entité Movie
                $serie = $this->setDataFromTmdbData($serieData);
            }

            // on persiste/met à jour
            $this->seriesRepository->add($serie);

            $series[] = $serie;
        }

        return $series;
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

    private function setDataFromTmdbData(array $serieData, Series $serie = null): Series
    {
        if ($serie == null) {
            $serie = new Series();
        }
        $genreNames = [];
        foreach ($serieData['genre_ids'] as $id) {
            $genreNames[] = TmdbGenres::getName($id);
        }

        if (!empty($serieData['production_companies'])) {
            foreach ($serieData['production_companies'] as $pcData) {
                $pc = $this->productionCompanieFactory->createFromTmdbData($pcData);
                $serie->addProductionCompany($pc);
            }
        }

        return $serie ->setTitle($serieData['name'] ?? '')
            ->setStatus($serieData['status'] ?? '')
            ->setTmdbId($serieData['id'] ?? '')
            ->setPopularity($serieData['popularity'] ?? '')
            ->setNumberEpisodes($serieData['number_of_episodes'] ?? 0)
            ->setNumberSeasons($serieData['number_of_seasons'] ?? 0)
            ->setPicture($serieData['poster_path'] ?? '')
            ->setResume($serieData['overview'] ?? '')
            ->setReleaseDate(isset($serieData['first_air_date']) ? new \DateTime($serieData['first_air_date']) : null);

    }
}
