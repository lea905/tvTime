<?php

namespace App\Controller;

use App\Repository\MovieRepository;
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
    public function index(): Response
    {
        return $this->render('index.html.twig', [

        ]);
    }

    #[Route('/synchronisationApi/movies', name: 'app_synchronisation_api_movies')]
    public function fetchMovies(): Response
    {
        $series = $this->tmdb->getData($this->token);

//        foreach ($series as $serie) {
//            $this->seriesRepository->save($serie, true);
//        }

        return $this->render('index.html.twig', [

        ]);
    }
}
