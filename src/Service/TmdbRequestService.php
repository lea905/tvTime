<?php

namespace App\Service;

use App\Entity\Episode;
use App\Entity\Movie;
use App\Entity\Season;
use App\Entity\Series;
use App\Factory\EpisodeFactory;
use App\Factory\MovieFactory;
use App\Factory\SeasonFactory;
use App\Factory\SeriesFactory;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use function Sodium\add;

class TmdbRequestService
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private MovieFactory        $movieFactory,
        private SeriesFactory       $seriesFactory,
        private SeasonFactory        $seasonFactory,
        private EpisodeFactory      $episodeFactory,
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

    public function getSerie(mixed $token, int $id) :Series
    {
        $response = $this->httpClient->request('GET', 'https://api.themoviedb.org/3/tv/' . $id, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'accept' => 'application/json',
            ],
        ]);

        $data = $response->toArray();
        return $this->seriesFactory->createFromOneTmdbData($data);
    }

    public function getSeason (mixed $token, int $idSerie, int $idSeason) :Season
    {
        $response = $this->httpClient->request('GET', 'https://api.themoviedb.org/3/tv/' . $idSerie .
            '/season/' . $idSeason, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'accept' => 'application/json',
            ],
        ]);

        $data = $response->toArray();
        return $this->seasonFactory->createFromTmdbData($data);
    }

    public function getEpisode(mixed $token, int $idSerie, int $idSeason, int $idEpisode) :Episode
    {
        $response = $this->httpClient->request('GET', 'https://api.themoviedb.org/3/tv/' . $idSerie .
            '/season/' . $idSeason . '/episode/{episode_number}' . $idEpisode, [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'accept' => 'application/json',
            ],
        ]);

        $data = $response->toArray();
        return $this->episodeFactory->createFromTmdbData($data);
    }
}
