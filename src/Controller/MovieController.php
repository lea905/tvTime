<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\ProductionCompanie;
use App\Repository\MovieRepository;
use App\Repository\ProductionCompanieRepository;
use App\Repository\WatchListRepository;
use App\Service\TmdbRequestService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/movies')]
class MovieController extends AbstractController
{

    private string $token;

    public function __construct(private readonly TmdbRequestService $tmdb,
                                private readonly MovieRepository    $movieRepository)
    {
        $this->token = $_ENV['TMDB_TOKEN'];
    }

    /**
     * Affiche tout les films présents dans la BDD
     *
     * @return Response
     */
    #[Route('', name: 'movies')]
    public function index(): Response
    {
        $allMovies = $this->movieRepository->findAll();
        $nowPlayingMovies = $this->movieRepository->findNowPlaying();

        return $this->render('movie/index.html.twig', [
            'allMovies' => $allMovies,
            'comedies' => $this->movieRepository->findByGenre('Comédie')
        ]);
    }

    #[Route('/', name: 'app_movie_index')]
    public function home(): Response
    {
        $nowPlayingMovies = $this->tmdb->getMoviesNowPlaying($this->token);
        $popularMovies = $this->tmdb->getMoviesPopular($this->token);
        $upcomingMovies = $this->tmdb->getMoviesUpcoming($this->token);

        return $this->render('movie/index.html.twig', [
            'now_playing_movies' => $nowPlayingMovies,
            'popular_movies' => $popularMovies,
            'upcoming_movies' => $upcomingMovies,
        ]);
    }

    /**
     * Affiche un film identifié par son id
     *
     * @param int $id
     * @return Response
     */
    #[Route('/show/{id}', name: 'app_movie_show')]
    public function show(int $id, WatchListRepository $watchListRepository): Response
    {
        $user = $this->getUser();

        $watchLists = [];
        if ($user) {
            $watchLists = $watchListRepository->findBy(['userId' => $user->getId()]);
        }

        return $this->render('movie/show.html.twig', [
            'movie' => $this->movieRepository->findOneById($id),
            'watch_lists' => $watchLists,
        ]);
    }

    /**
     * Affiche les films du genre souhaité
     *
     * @param string $genre
     * @return Response
     */
    #[Route('/search/{genre}', name: 'app_movies_genre')]
    public function genre(string $genre): Response
    {
        $movies = $this->movieRepository->findByGenre($genre);
        if ($movies === null) {
            throw new NotFoundHttpException();
        }

        return $this->render('movie/genre.html.twig', [
            'movies' => $movies,
            'genre' => $genre,
        ]);
    }

    /**
     * Affiche les films actuellement au cinéma
     *
     * @return Response
     */
    #[Route('/now_playing', name: 'app_movies_now_playing')]
    public function now_playing(): Response
    {
        $movies = $this->tmdb->getMoviesNowPlaying($this->token);

        return $this->render('movie/now_playing.html.twig', [
            'movies' => $movies,
        ]);
    }

    /**
     * Affiche les films pas encore sortis
     *
     * @return Response
     */
    #[Route('/upcoming', name: 'app_movies_upcoming')]
    public function upcoming(): Response
    {
        $movies = $this->tmdb->getMoviesUpcoming($this->token);

        return $this->render('movie/upcoming.html.twig', [
            'movies' => $movies,
        ]);
    }

    #[Route('/synchronisationApi', name: 'app_movies_synchronisation_api')]
    public function fetchMovies(): Response
    {
        $this->tmdb->getData($this->token);
        return $this->redirectToRoute('movies');
    }
}
