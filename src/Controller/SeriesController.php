<?php

namespace App\Controller;

use App\Repository\SeriesRepository;
use App\Repository\WatchListRepository;
use App\Service\TmdbRequestService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
     *
     * @return Response
     */
    #[Route('', name: 'app_index_series')]
    public function index(): Response
    {

        $popularSeries = $this->tmdb->getSeriesPopular($this->token);

        return $this->render('series/index.html.twig', [
            'popular_series' => $popularSeries,
        ]);
    }

    /**
     * Affiche une série identifiée par son id
     *
     * @param int $id
     * @return Response
     */
    #[Route('/show/{id}', name: 'app_series_show')]
    public function show(int $id,  WatchListRepository $watchListRepository): Response
    {
        $user = $this->getUser();

        $watchLists = [];
        if ($user) {
            $watchLists = $watchListRepository->findBy(['userId' => $user->getId()]);
        }

        return $this->render('series/show.html.twig', [
            'serie' => $this->tmdb->getSerie($this->token, $id),
            'watch_lists' => $watchLists,
        ]);
    }

    /**
     * Affiche les séries du genre souhaité
     *
     * @param string $genre
     * @return Response
     */
    #[Route('/search/{genre}', name: 'app_series_genre')]
    public function genre(string $genre): Response
    {
        $series = $this->seriesRepository->findByGenre($genre);
        if($series === null) {
            throw new NotFoundHttpException();
        }

        return $this->render('series/genre.html.twig', [
            'series' => $series,
            'genre' => $genre,
        ]);
    }

    /**
     * Affiche les séries pas encore sorties
     *
     * @return Response
     */
    #[Route('/popular', name: 'app_series_popular')]
    public function popular(): Response
    {
        $series = $this->tmdb->getSeriesPopular($this->token);

        return $this->render('series/popular.html.twig', [
            'series' => $series,
        ]);
    }
}
