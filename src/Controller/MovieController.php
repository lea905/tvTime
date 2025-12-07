<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\ProductionCompanie;
use App\Entity\View;
use App\Form\EmotionType;
use App\Repository\MovieRepository;
use App\Repository\ProductionCompanieRepository;
use App\Repository\WatchListRepository;
use App\Service\TmdbRequestService;
use App\Utils\TmdbGenres;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    #[Route('/', name: 'app_movie_index')]
    public function home(Request $request): Response
    {
        $selectedGenre = $request->query->get('genre');

        if ($selectedGenre) {
            $movieGenre = $this->movieRepository->findByGenre($selectedGenre);
        } else {
            $movieGenre = $this->movieRepository->findAll();
        }

        $allMovies = $this->movieRepository->findAll();
        if (count($allMovies) <= 0)
            $this->fetchMovies();

        $popular = $this->movieRepository->findMostPopular(20);
        $year2025 = $this->movieRepository->findByYear(2025);
        $upcoming = $this->movieRepository->findUpcoming();
        $allGenres = TmdbGenres::getGenres();

        return $this->render('movie/index.html.twig', [
            'allMovies' => $allMovies,
            'popular' => $popular,
            'year2025' => $year2025,
            'upcoming' => $upcoming,
            'allGenres' => $allGenres,
            'selectedGenre' => $selectedGenre,
            'movieGenre' => $movieGenre,
        ]);
    }

    /**
     * Affiche un film identifié par son id
     *
     * @param int $id
     * @return Response
     */
    #[Route('/show/{id}', name: 'app_movie_show')]
    public function show(int $id, WatchListRepository $watchListRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $movie = $this->movieRepository->findOneById($id);

        $alreadySeen = false;
        $currentEmotion = null;

        if ($user && $movie) {
            $qb = $entityManager->getRepository(View::class)->createQueryBuilder('v')
                ->join('v.userId', 'u')
                ->join('v.movieId', 'm')
                ->where('u = :user')
                ->andWhere('m = :movie')
                ->andWhere('v.see = :see')
                ->setParameter('user', $user)
                ->setParameter('movie', $movie)
                ->setParameter('see', true)
                ->setMaxResults(1);

            $view = $qb->getQuery()->getOneOrNullResult();

            if ($view) {
                $alreadySeen = $view->isSee();
                $currentEmotion = $view->getEmotions();
            }
        }

        $watchLists = [];
        if ($user) {
            $watchLists = $watchListRepository->findBy(['userId' => $user->getId()]);
        }

        $emotionForm = $this->createForm(EmotionType::class, [
            'emotion' => $currentEmotion,
        ]);

        return $this->render('movie/show.html.twig', [
            'movie' => $movie,
            'watch_lists' => $watchLists,
            'already_seen' => $alreadySeen,
            'emotion_form' => $emotionForm,
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

//    /**
//     * Affiche les films actuellement au cinéma
//     *
//     * @return Response
//     */
//    #[Route('/now_playing', name: 'app_movies_now_playing')]
//    public function now_playing(): Response
//    {
//        $movies = $this->tmdb->getMoviesNowPlaying($this->token);
//
//        return $this->render('movie/now_playing.html.twig', [
//            'movies' => $movies,
//        ]);
//    }

//    /**
//     * Affiche les films pas encore sortis
//     *
//     * @return Response
//     */
//    #[Route('/upcoming', name: 'app_movies_upcoming')]
//    public function upcoming(): Response
//    {
//        $movies = $this->tmdb->getMoviesUpcoming($this->token);
//
//        return $this->render('movie/upcoming.html.twig', [
//            'movies' => $movies,
//        ]);
//    }

    #[Route('/synchronisationApi', name: 'app_movies_synchronisation_api')]
    public function fetchMovies(): Response
    {
        $this->tmdb->getMoviesData($this->token);
        return $this->redirectToRoute('app_movie_index');
    }
}
