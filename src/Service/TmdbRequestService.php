<?php

namespace App\Service;

use App\Entity\Episode;
use App\Entity\Season;
use App\Entity\Series;
use App\Factory\EpisodeFactory;
use App\Factory\MovieFactory;
use App\Factory\SeriesFactory;
use App\Utils\TmdbGenres;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TmdbRequestService
{
    private $cpt = 30; // number of datas *20 come from API

    public function __construct(
        private HttpClientInterface $httpClient,
        private MovieFactory        $movieFactory,
        private SeriesFactory       $seriesFactory,
        private EpisodeFactory      $episodeFactory,
    )
    {
    }

    /**
     * Get the episode's datas
     */
    public function getEpisode(mixed $token, int $idSerie, Season $season, int $idEpisode): Episode
    {
        $response = $this->httpClient->request('GET', 'https://api.themoviedb.org/3/tv/' . $idSerie .
            '/season/' . $season->getNumber() . '/episode/' . $idEpisode, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'accept' => 'application/json',
            ], 'query' => [
                'language' => 'fr-FR',
            ]
        ]);

        $data = $response->toArray();
        return $this->episodeFactory->createDataFromTmdbData($data, $season);
    }

    /**
     * Fill the database with cpt * 20 movies of the API
     */
    public function getMoviesData(mixed $token) :Array
    {
        $datas = [];
        $temp = 0;
        while ($temp++ < $this->cpt) {
            // Movies
            $response = $this->httpClient->request('GET', 'https://api.themoviedb.org/3/discover/movie', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'accept' => 'application/json',
                ], 'query' => [
                    'language' => 'fr-FR',
                    'page' => $this->cpt,
                ],]);
            $data = $response->toArray();
            $datas = array_merge($datas, $this->movieFactory->createMultipleFromTmdbData($data));
        }
        return $datas;
    }

    /**
     * Fill the database with cpt * 20 series of the API
     */
    public function getSeriesData(mixed $token)
    {
        $datas = [];
        $temp = 0;
        while ($temp++ < $this->cpt) {
            //Series
            $response = $this->httpClient->request('GET', 'https://api.themoviedb.org/3/discover/tv', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'accept' => 'application/json',
                ],
                'query' => [
                    'language' => 'fr-FR',
                    'page' => $this->cpt,
                ],
            ]);
            $data = $response->toArray();
            $series = $this->seriesFactory->createMultipleFromTmdbData($data);
            $datas = array_merge($datas, $series);
        }
        return $datas;
    }

    public function genres(mixed $token)
    {
        // Movies
        $response = $this->httpClient->request('GET', 'https://api.themoviedb.org/3/genre/movie/list', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'accept' => 'application/json',
            ],
            'query' => [
                'language' => 'fr-FR',
            ],
        ]);

        $data = $response->toArray();
        TmdbGenres::fillDatas($data);

        // Series
        $response = $this->httpClient->request('GET', 'https://api.themoviedb.org/3/genre/tv/list', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'accept' => 'application/json',
            ],
            'query' => [
                'language' => 'fr-FR',
            ],
        ]);

        $data = $response->toArray();
        TmdbGenres::fillDatas($data);
    }
    /**
     * Update series' datas and its seasons
     */
    public function getSerie(mixed $token, int $id): Series
    {
        $response = $this->httpClient->request('GET', 'https://api.themoviedb.org/3/tv/' . $id, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'accept' => 'application/json',
            ],
            'query' => [
                'language' => 'fr-FR',
            ],
        ]);

        $data = $response->toArray();
        return $this->seriesFactory->setOrCreate($data);
    }
}
