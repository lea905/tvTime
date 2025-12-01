<?php

namespace App\Controller;

use App\Repository\SeasonRepository;
use App\Service\TmdbRequestService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/season')]
class SeasonController extends AbstractController
{
    private string $token;

    public function __construct(private readonly TmdbRequestService $tmdb,
                                private readonly SeasonRepository   $seasonRepository)
    {
        $this->token = $_ENV['TMDB_TOKEN'];
    }

    /**
     * Affiche une saison d'une série identifiée par les ids
     *
     * @param int $idSerie
     * @param int $idSeason
     * @return Response
     */
    #[Route('/show/{idSerie}/{idSeason}', name: 'app_season_show')]
    public function show(int $idSerie, int $idSeason): Response
    {
        return $this->render('season/show.html.twig', [
            'season' => $this->tmdb->getSeason($this->token, $idSerie, $idSeason),
            'idSerie' => $idSerie,
        ]);
    }
}
