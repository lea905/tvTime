<?php

namespace App\Controller;

use App\Entity\View;
use App\Form\EmotionType;
use App\Repository\SeriesRepository;
use App\Repository\WatchListRepository;
use App\Service\TmdbRequestService;
use App\Utils\TmdbGenres;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/series')]
class SeriesController extends AbstractController
{

    private string $token;

    public function __construct(private readonly TmdbRequestService $tmdb,
                                private readonly SeriesRepository   $seriesRepository)
    {
        $this->token = $_ENV['TMDB_TOKEN'];
    }

    /**
     * Affiche toutes les séries présents dans la BDD
     */
    #[Route('', name: 'app_index_series')]
    public function index(Request $request): Response
    {

        $series = $this->seriesRepository->findAll();

        if (count($series) <= 0)
            $this->fetchSeries();

        $selectedGenre = $request->query->get('genre');

        if ($selectedGenre) {
            $seriesGenre = $this->seriesRepository->findByGenre($selectedGenre);
        } else {
            $seriesGenre = $this->seriesRepository->findAll();
        }

        if ($series === null) {
            throw new NotFoundHttpException();
        }

        $popular = $this->seriesRepository->findMostPopular(20);
        $year2025 = $this->seriesRepository->findByYear(2025);
        $upcoming = $this->seriesRepository->findUpcoming();

        return $this->render('series/index.html.twig', [
            'series' => $series,
            'popular' => $popular,
            'year2025' => $year2025,
            'upcoming' => $upcoming,
            'selectedGenre' => $selectedGenre,
            'allGenres' => TmdbGenres::getGenres(),
            'seriesGenre' => $seriesGenre,
        ]);
    }

    /**
     * Affiche une série identifiée par son id
     */
    #[Route('/show/{id}', name: 'app_series_show')]
    public function show(int $id, WatchListRepository $watchListRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $serie = $this->seriesRepository->findOneById($id);
        if (!$serie)
            throw new NotFoundHttpException();
        if ($serie->getStatus() == null)
            $this->tmdb->getSerie($this->token, $serie->getTmdbId());

        $alreadySeen = false;
        $currentEmotion = null;

        if ($user && $serie) {
            $qb = $entityManager->getRepository(View::class)->createQueryBuilder('v')
                ->where(':serie MEMBER OF v.seriesId')
                ->andWhere('v.userId = :user')
                ->andWhere('v.see = :see')
                ->setParameter('serie', $serie)
                ->setParameter('user', $user)
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

        return $this->render('series/show.html.twig', [
            'serie' => $serie,
            'watch_lists' => $watchLists,
            'already_seen' => $alreadySeen,
            'emotion_form' => $emotionForm,
        ]);
    }

    /**
     * Affiche les séries du genre souhaité
     */
    #[Route('/search/{genre}', name: 'app_series_genre')]
    public function genre(string $genre): Response
    {
        $series = $this->seriesRepository->findByGenre($genre);
        if ($series === null) {
            throw new NotFoundHttpException();
        }

        return $this->render('series/genre.html.twig', [
            'series' => $series,
            'genre' => $genre,
        ]);
    }

    /**
     * Prend les informations des séries de l'API
     */
    #[Route('/synchronisationApi', name: 'app_series_synchronisation_api')]
    public function fetchSeries(): Response
    {
        $this->tmdb->getSeriesData($this->token);
        return $this->redirectToRoute('app_index_series');
    }
}
