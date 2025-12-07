<?php

namespace App\Service;

use App\Entity\Episode;
use App\Entity\Movie;
use App\Entity\Season;
use App\Entity\Series;
use App\Factory\EpisodeFactory;
use App\Factory\MovieFactory;
use App\Factory\SeriesFactory;
use App\Repository\EpisodeRepository;
use App\Repository\SeriesRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TmdbRequestService
{
    private $cpt = 1; // number of datas *20 come from API

    public function __construct(
        private HttpClientInterface $httpClient,
        private MovieFactory        $movieFactory,
        private SeriesFactory       $seriesFactory,
        private EpisodeFactory      $episodeFactory,
        private SeriesRepository    $seriesRepository,
        private EpisodeRepository   $episodeRepository
    )
    {
    }

    public function getMoviesNowPlaying(string $token): array
    {
        $response = $this->httpClient->request('GET', 'https://api.themoviedb.org/3/movie/now_playing', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'accept' => 'application/json',
            ],
            'query' => [
                'language' => 'fr-FR',
                'page' => 1,
            ],
        ]);

        $data = $response->toArray();

        // Ici on renvoie une liste d’objets Movie
        return $this->movieFactory->createMultipleFromTmdbData($data);
    }

    public function getMoviesUpcoming(string $token): array
    {
        $response = $this->httpClient->request('GET', 'https://api.themoviedb.org/3/movie/upcoming', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'accept' => 'application/json',
            ],
            'query' => [
                'language' => 'fr-FR',
                'page' => 1,
            ],
        ]);

        $data = $response->toArray();

        // Ici on renvoie une liste d’objets Movie
        return $this->movieFactory->createMultipleFromTmdbData($data);
    }

    public function getMovie(string $token, string $id): Movie
    {
        $response = $this->httpClient->request('GET', 'https://api.themoviedb.org/3/movie/' . $id, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'accept' => 'application/json',
            ],
            'query' => [
                'language' => 'fr-FR',
                'page' => 1,
            ],
        ]);

        $data = $response->toArray();
//        $res = ['production_companies' => $data["production_companies"], 'status' => $data["status"]];

        return $this->movieFactory->createFromOneTmdbData($data);
    }

    public function getMoviesPopular(string $token): array
    {
        $response = $this->httpClient->request('GET', 'https://api.themoviedb.org/3/movie/popular', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'accept' => 'application/json',
            ],
            'query' => [
                'language' => 'fr-FR',
                'page' => 1,
            ],
        ]);

        $data = $response->toArray();

        return $this->movieFactory->createMultipleFromTmdbData($data);
    }


    public function getSeriesPopular(string $token): array
    {
        $response = $this->httpClient->request('GET', 'https://api.themoviedb.org/3/tv/popular', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'accept' => 'application/json',
            ],
            'query' => [
                'language' => 'fr-FR',
                'page' => 1,
            ],
        ]);

        $data = $response->toArray();

        // Ici on renvoie une liste d’objets Series
        return $this->seriesFactory->createMultipleFromTmdbData($data);
    }

    public function getEpisode(mixed $token, int $idSerie, Season $season, int $idEpisode): Episode
    {
        $response = $this->httpClient->request('GET', 'https://api.themoviedb.org/3/tv/' . $idSerie .
            '/season/' . $season->getNumber() . '/episode/' . $idEpisode, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'accept' => 'application/json',
            ],
        ]);

        $data = $response->toArray();
        return $this->episodeFactory->createDataFromTmdbData($data, $season);
    }

    /**
     * Fill the database with cpt * 20 movies of the API
     *
     * @param mixed $token
     * @return array
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getMoviesData(mixed $token) :Array
    {
        $datas = [];
        while ($this->cpt++ < 2) {
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
     *
     * @param mixed $token
     * @return array
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
     */
    public function getSeriesData(mixed $token)
    {
        $datas = [];
        while ($this->cpt++ < 2) {
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

    /**
     * Update series' datas and its seasons
     *
     * @param mixed $token
     * @param int $id
     * @return Series
     * @throws \Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface
     * @throws \Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface
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
