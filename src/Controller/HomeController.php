<?php
namespace App\Controller;

use App\Repository\MovieRepository;
use App\Repository\WatchListRepository;
use App\Service\TmdbRequestService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private string $token;

    public function __construct(private readonly TmdbRequestService $tmdb,
                                private readonly MovieRepository    $movieRepository)
    {
        $this->token = $_ENV['TMDB_TOKEN'];
    }
    #[Route('/', name: 'app_home')]
    public function index(WatchListRepository $watchListRepository): Response
    {
        $user = $this->getUser();

        $watchLists = [];
        if ($user) {
            $watchLists = $watchListRepository->findBy(['userId' => $user->getId()]);
        }

        $nowPlayingMovies = $this->tmdb->getMoviesNowPlaying($this->token);
        $popularMovies = $this->tmdb->getMoviesPopular($this->token);
        $upcomingMovies = $this->tmdb->getMoviesUpcoming($this->token);
        $popularSeries = $this->tmdb->getSeriesPopular($this->token);

        return $this->render('index.html.twig', [
            'watch_lists' => $watchLists,
            'now_playing_movies' => $nowPlayingMovies,
            'popular_movies'     => $popularMovies,
            'upcoming_movies'    => $upcomingMovies,
            'popular_series' => $popularSeries,
        ]);
    }
}
