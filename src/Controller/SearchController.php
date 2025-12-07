<?php

namespace App\Controller;

use App\Repository\MovieRepository;
use App\Repository\SeriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    #[Route('/search', name: 'app_search', methods: ['GET'])]
    public function index(Request $request, MovieRepository $movieRepository, SeriesRepository $seriesRepository): Response
    {
        $query = $request->query->get('q');
        $movies = [];
        $series = [];

        if ($query) {
            $movies = $movieRepository->searchByTitle($query);
            $series = $seriesRepository->searchByTitle($query);
        }

        return $this->render('search/index.html.twig', [
            'query' => $query,
            'movies' => $movies,
            'series' => $series,
        ]);
    }
}
