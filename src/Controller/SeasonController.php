<?php

namespace App\Controller;

use App\Repository\EpisodeRepository;
use App\Repository\SeasonRepository;
use App\Repository\SeriesRepository;
use App\Service\TmdbRequestService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/season')]
class SeasonController extends AbstractController
{
    private string $token;

    public function __construct(private readonly TmdbRequestService $tmdb,
                                private readonly SeasonRepository   $seasonRepository,
                                private readonly SeriesRepository   $seriesRepository,
                                private readonly EpisodeRepository  $episodeRepository,)
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

        $series = $this->seriesRepository->find($idSerie);

        if (!$series) {
            throw $this->createNotFoundException("Série introuvable");
        }

        $season = $this->seasonRepository->findOneBy([
            'number' => $idSeason,
            'seriesId' => $series,
        ]);
        $tmdbSerieId = $this->seriesRepository->findOneById($idSerie)->getTmdbId();

        if (!$season) {
            throw $this->createNotFoundException("Saison introuvable pour cette série");
        }
        if (!$tmdbSerieId) {
            throw $this->createNotFoundException("Identifiant Tmdb de la serie numéro " . $idSerie . " introuvable");
        }

        // get its episodes
        $cptEpisode = $season->getNumberEpisodes();
        if ($cptEpisode > 0) {
            for ($j = 1; $j <= $cptEpisode; $j++) {
                $existing = $this->episodeRepository->findOneBy(['number' => $j, 'season' => $season]);

                // if the episode wasn't in the database before, we search it in the API's datas
                if (!$existing) {
                    $episode = $this->tmdb->getEpisode($this->token, $tmdbSerieId, $season, $j);
                    $episode->setSeason($season);
                    $season->addEpisode($episode);
                    $this->episodeRepository->add($episode);
                }
            }
        }

        return $this->render('season/show.html.twig', [
            'season' => $season,
            'idSerie' => $idSerie,
        ]);
    }
}
