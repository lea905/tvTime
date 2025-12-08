<?php

namespace App\Controller;

use App\Repository\MovieRepository;
use App\Repository\SeriesRepository;
use App\Service\TmdbRequestService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private string $token;

    public function __construct(private readonly TmdbRequestService $tmdb,
                                private readonly MovieRepository    $movieRepository,
                                private readonly SeriesRepository   $seriesRepository)
    {
        $this->token = $_ENV['TMDB_TOKEN'];
    }

    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $allMovies = $this->movieRepository->findAll();
        $allSeries = $this->seriesRepository->findAll();

        return $this->render('index.html.twig', [
            'allMovies' => $allMovies,
            'allSeries' => $allSeries,
        ]);
    }
}
