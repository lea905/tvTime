<?php
namespace App\Controller;

use App\Entity\View;
use App\Entity\Movie;
use App\Entity\Series;
use App\Form\EmotionType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use App\Enum\emotion;
use App\Entity\Episode;
use Symfony\Component\HttpFoundation\Request;

class ViewController extends AbstractController
{
    #[Route('/movie/{id}/seen', name: 'app_movie_seen', methods: ['POST'])]
    public function movieSeen(Movie $movie, EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(EmotionType::class);
        $form->handleRequest($request);

        $emotions = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $emotions = $form->get('emotion')->getData();
        }

        $viewRepo = $entityManager->getRepository(View::class);

        $view = $viewRepo->createQueryBuilder('v')
            ->join('v.userId', 'u')
            ->join('v.movieId', 'm')
            ->where('u = :user')
            ->andWhere('m = :movie')
            ->setParameter('user', $user)
            ->setParameter('movie', $movie)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$view) {
            $view = new View();
            $view->setSee(true);
            $view->setDateSee(new \DateTime());

            if (!empty($emotions)) {
                $view->setEmotions($emotions);
            }

            $view->setUserId($user);
            $view->addMovieId($movie);

            $entityManager->persist($view);
        } else {
            $view->setSee(true);
            $view->setDateSee(new \DateTime());

            if (!empty($emotions)) {
                $view->setEmotions($emotions);
            }
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_movie_show', ['id' => $movie->getId()]);
    }

//    #[Route('/series/{id}/seen', name: 'app_series_seen', methods: ['POST'])]
//    public function seriesSeen(Series $serie, EntityManagerInterface $entityManager, Request $request): Response
//    {
//        $user = $this->getUser();
//        if (!$user) {
//            throw $this->createAccessDeniedException();
//        }
//
//        $form = $this->createForm(EmotionType::class);
//        $form->handleRequest($request);
//
//        $emotions = [];
//        if ($form->isSubmitted() && $form->isValid()) {
//            $emotions = $form->get('emotion')->getData();
//        }
//
//        $viewRepo = $entityManager->getRepository(View::class);
//
////        $view = $viewRepo->createQueryBuilder('v')
////            ->join('v.userId', 'u')
////            ->join('v.seriesId', 'm')
////            ->where('u = :user')
////            ->andWhere('m = :series')
////            ->setParameter('user', $user)
////            ->setParameter('series', $serie)
////            ->setMaxResults(1)
////            ->getQuery()
////            ->getOneOrNullResult();
//
//        $view = $viewRepo->createQueryBuilder('v')
//            ->where(':serie MEMBER OF v.seriesId')
//            ->andWhere('v.userId = :user')
//            ->setParameter('serie', $serie)
//            ->setParameter('user', $user)
//            ->setMaxResults(1)
//            ->getQuery()
//            ->getOneOrNullResult();
//
//        if (!$view) {
//            $view = new View();
//            $view->setSee(true);
//            $view->setDateSee(new \DateTime());
//
//            if (!empty($emotions)) {
//                $view->setEmotions($emotions);
//            }
//
//            $view->setUserId($user);
//            $view->addSeriesId($serie);
//
//            $entityManager->persist($view);
//        } else {
//            $view->setSee(true);
//            $view->setDateSee(new \DateTime());
//
//            if (!empty($emotions)) {
//                $view->setEmotions($emotions);
//            }
//        }
//
//        $entityManager->flush();
//
//        return $this->redirectToRoute('app_series_show', ['id' => $serie->getId()]);
//    }

    #[Route('/series/{id}/seen', name: 'app_serie_seen', methods: ['POST'])]
    public function seriesSeen(Series $serie, EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(EmotionType::class);
        $form->handleRequest($request);

        $emotions = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $emotions = $form->get('emotion')->getData();
        }

        $viewRepo = $entityManager->getRepository(View::class);

        // Vérifie si l'utilisateur a déjà vu cette série
        $view = $viewRepo->createQueryBuilder('v')
            ->where(':serie MEMBER OF v.seriesId')
            ->andWhere('v.userId = :user')
            ->setParameter('serie', $serie)
            ->setParameter('user', $user)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$view) {
            $view = new View();
            $view->setSee(true);
            $view->setDateSee(new \DateTime());
            $view->setUserId($user);
            $view->addSeriesId($serie);

            if (!empty($emotions)) {
                $view->setEmotions($emotions);
            }

            $entityManager->persist($view);
        } else {
            $view->setSee(true);
            $view->setDateSee(new \DateTime());
            if (!empty($emotions)) {
                $view->setEmotions($emotions);
            }
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_series_show', ['id' => $serie->getId()]);
    }

    #[Route('/episode/{id}/seen', name: 'app_episode_seen', methods: ['POST'])]
    public function episodeSeen(Episode $episode, EntityManagerInterface $entityManager, Request $request): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $form = $this->createForm(EmotionType::class);
        $form->handleRequest($request);

        $emotions = [];
        if ($form->isSubmitted() && $form->isValid()) {
            $emotions = $form->get('emotion')->getData();
        }

        $viewRepo = $entityManager->getRepository(View::class);

        $view = $viewRepo->createQueryBuilder('v')
            ->join('v.userId', 'u')
            ->join('v.episodeId', 'e')
            ->where('u = :user')
            ->andWhere('e = :episode')
            ->setParameter('user', $user)
            ->setParameter('episode', $episode)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$view) {
            $view = new View();
            $view->setSee(true);
            $view->setDateSee(new \DateTime());

            if (!empty($emotions)) {
                $view->setEmotions($emotions);
            }

            $view->setUserId($user);
            $view->addEpisode($episode);

            $entityManager->persist($view);
        } else {
            $view->setSee(true);
            $view->setDateSee(new \DateTime());

            if (!empty($emotions)) {
                $view->setEmotions($emotions);
            }
        }

        $entityManager->flush();

        // Redirection vers la saison de l'épisode
        return $this->redirectToRoute('app_season_show', [
            'idSerie' => $episode->getSeason()->getSeriesId()->getTmdbId(),
            'idSeason' => $episode->getSeason()->getNumber()
        ]);
    }
}
