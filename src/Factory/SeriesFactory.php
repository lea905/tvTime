<?php

namespace App\Factory;

use App\Entity\Series;
use App\Repository\SeasonRepository;
use App\Repository\SeriesRepository;
use App\Utils\TmdbGenres;

class SeriesFactory
{
    public function __construct(
        private SeriesRepository          $seriesRepository,
        private SeasonFactory             $seasonFactory,
        private SeasonRepository          $seasonRepository,
        private ProductionCompanieFactory $productionCompanieFactory,
        private CreatorFactory            $creatorFactory,
    )
    {
    }

    public function createMultipleFromTmdbData(array $data): array
    {
        $series = [];

        foreach ($data['results'] as $serieData) {
            $series[] = $this->setOrCreate($serieData);
        }

        return $series;
    }

    public function setOrCreate(array $serieData): Series
    {
        // on cherche si le film existe déjà en BDD
        $existingSeries = $this->seriesRepository->findOneBy(['tmdbId' => $serieData['id']]);

        if ($existingSeries) {
            // mise à jour de l'entité existante
            $serie = $this->setDataFromTmdbData($serieData, $existingSeries);
        } else {
            // création d'une nouvelle entité Movie
            $serie = $this->createDataFromTmdbData($serieData);
        }

        // on persiste/met à jour
        $this->seriesRepository->add($serie);
        return $serie;
    }

    public function createDataFromTmdbData(array $serieData): Series
    {
        $serie = new Series();

        $genreNames = [];
        if (isset($serieData['genre_ids'])) {
            foreach ($serieData['genre_ids'] as $number => $id) {
                $tempGenre = TmdbGenres::getName($id);
                if($tempGenre != null) {
                    $genreNames[] = $tempGenre;
                }
            }
        };

        $serie->setGenres($genreNames);

        // Creators
        if (!empty($serieData['created_by'])) {
            foreach ($serieData['created_by'] as $creatorData) {
                $creator = $this->creatorFactory->createFromTmdbData($creatorData);
                $serie->addCreator($creator);
            }
        }

        // Production Companies
        if (!empty($serieData['production_companies'])) {
            foreach ($serieData['production_companies'] as $pcData) {
                $pc = $this->productionCompanieFactory->createFromTmdbData($pcData);
                $serie->addProductionCompany($pc);
            }
        }

        return $serie
            ->setTitle($serieData['name'] ?? '')
            ->setStatus($serieData['status'] ?? '')
            ->setTmdbId($serieData['id'] ?? 0)
            ->setPopularity($serieData['popularity'] ?? 0)
            ->setNumberEpisodes($serieData['number_of_episodes'] ?? 0)
            ->setNumberSeasons($serieData['number_of_seasons'] ?? 0)
            ->setPicture($serieData['poster_path'] ?? '')
            ->setResume(substr($serieData['overview'] ?? '', 0, 255))
            ->setReleaseDate(
                !empty($serieData['first_air_date'])
                    ? new \DateTime($serieData['first_air_date'])
                    : null
            );
    }

    public function setDataFromTmdbData(array $serieData, Series $serie): Series
    {
        if (isset($serieData['genres'])) {
            foreach ($serieData['genres'] as $genre) {
                $serie->addGenre($genre['name']);
            }
        }

        $serie->getCreators()->clear();
        if (!empty($serieData['created_by'])) {
            foreach ($serieData['created_by'] as $creatorData) {
                $creator = $this->creatorFactory->createFromTmdbData($creatorData);
                $serie->addCreator($creator);
            }
        }

        $serie->getProductionCompanies()->clear();
        if (!empty($serieData['production_companies'])) {
            foreach ($serieData['production_companies'] as $pcData) {
                $pc = $this->productionCompanieFactory->createFromTmdbData($pcData);
                $serie->addProductionCompany($pc);
            }
        }

        foreach ($serie->getSeasons() as $oldSeason) {
            $serie->removeSeason($oldSeason);
        }

        if (!empty($serieData['seasons'])) {
            foreach ($serieData['seasons'] as $seasonData) {
                $season = $this->seasonFactory->createDataFromTmdbData($seasonData);
                $season->setSeriesId($serie);
                $serie->addSeason($season);
                $this->seasonRepository->add($season);
            }
        }

        if (isset($serieData['name'])) {
            $serie->setTitle($serieData['name']);
        }
        if (isset($serieData['status'])) {
            $serie->setStatus($serieData['status']);
        }
        if (isset($serieData['popularity'])) {
            $serie->setPopularity($serieData['popularity']);
        }
        if (isset($serieData['poster_path'])) {
            $serie->setPicture($serieData['poster_path']);
        }
        if (isset($serieData['overview'])) {
            $serie->setResume(substr($serieData['overview'] ?? '', 0, 255));
        }
        if (isset($serieData['id'])) {
            $serie->setTmdbId($serieData['id']);
        }
        if (!empty($serieData['first_air_date'])) {
            $serie->setReleaseDate(new \DateTime($serieData['first_air_date']));
        }

        $serie
            ->setNumberEpisodes($serieData['number_of_episodes'] ?? 0)
            ->setNumberSeasons($serieData['number_of_seasons'] ?? 0);
        return $serie;
    }
}
