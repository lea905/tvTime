<?php

namespace App\Controller;

use App\Entity\Movie;
use App\Entity\Series;
use App\Entity\WatchList;
use App\Form\WatchListType;
use App\Repository\WatchListRepository;
use App\Service\TmdbRequestService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/watch/list')]
final class WatchListController extends AbstractController
{

    public function __construct(
        private TmdbRequestService $tmdbRequestService,
    ) {}

    #[Route(name: 'app_watch_list_index', methods: ['GET'])]
    public function index(WatchListRepository $watchListRepository): Response
    {
        return $this->render('watch_list/index.html.twig', [
            'watch_lists' => $watchListRepository->findBy(['userId' => $this->getUser()]),
        ]);
    }

    #[Route('/new', name: 'app_watch_list_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $watchList = new WatchList();
        $watchList->setUserId($this->getUser());
        $form = $this->createForm(WatchListType::class, $watchList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($watchList);
            $entityManager->flush();

            return $this->redirectToRoute('app_watch_list_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('watch_list/new.html.twig', [
            'watch_list' => $watchList,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_watch_list_show', methods: ['GET'])]
    public function show(WatchList $watchList): Response
    {
        return $this->render('watch_list/show.html.twig', [
            'watch_list' => $watchList,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_watch_list_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, WatchList $watchList, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(WatchListType::class, $watchList);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_watch_list_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('watch_list/edit.html.twig', [
            'watch_list' => $watchList,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_watch_list_delete', methods: ['POST'])]
    public function delete(Request $request, WatchList $watchList, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$watchList->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($watchList);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_watch_list_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/watchlists/{id}/add', name: 'watchlist_add_item', methods: ['POST'])]
    public function addToList(int $id, Request $request, EntityManagerInterface $entityManager, WatchListRepository $watchListRepository, TmdbRequestService $tmdbRequestService,): JsonResponse
    {
        $user = $this->getUser();
        if (!$user) {
            return new JsonResponse(['error' => 'Non connecté'], 401);
        }

        $data        = json_decode($request->getContent(), true);
        $tmdbId      = $data['tmdbId'] ?? null;
        $type        = $data['type'] ?? null;
        $newListName = $data['newListName'] ?? null;

        $watchList = null;
        if ($id !== 0) {
            $watchList = $watchListRepository->findOneBy([
                'id'     => $id,
                'userId' => $this->getUser(),
            ]);
            if (!$watchList) {
                return new JsonResponse(['error' => 'Liste introuvable'], 404);
            }
        } else {
            if (!$newListName) {
                return new JsonResponse(['error' => 'Nom de liste manquant'], 400);
            }
            $watchList = new WatchList();
            $watchList->setTitle($newListName);
            $watchList->setDescription(null);
            $watchList->setUserId($this->getUser());
            $entityManager->persist($watchList);
        }

        $movieRepo = $entityManager->getRepository(Movie::class);
        $seriesRepo = $entityManager->getRepository(Series::class);
        $movie = $movieRepo->findOneBy(['tmdbId' => $tmdbId]);
        $series = $seriesRepo->findOneBy(['tmdbId' => $tmdbId]);
        $token = $_ENV['TMDB_TOKEN'];

        if ($type === 'movie') {
            if (!$movie) {
                $movie = $this->tmdbRequestService->getMovie($token, (string) $tmdbId);
                $entityManager->persist($movie);
            }

            $watchList->addMovie($movie);
        }

        if ($type === 'series') {
            if (!$series) {
                $series = $seriesRepo->findOneBy(['tmdbId' => $tmdbId]);
                $entityManager->persist($series);
            }

            $watchList->addSeries($series);
        }

        $entityManager->flush();

        return new JsonResponse([
            'status'        => 'ok',
            'createdListId' => $id === 0 ? $watchList->getId() : null,
        ]);
    }
    #[Route('/watchlists/{id}/delete', name: 'watchlist_del_item', methods: ['POST'])]
    public function delToList(int $id, Request $request, EntityManagerInterface $entityManager, WatchListRepository $watchListRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $watchList = $watchListRepository->findOneBy([
            'id'     => $id,
            'userId' => $user,
        ]);

        if (!$watchList) {
            throw $this->createNotFoundException('Liste introuvable');
        }

        $itemId = $request->request->get('itemId');
        $type   = $request->request->get('type');

        if (!$itemId || !in_array($type, ['movie', 'series'], true)) {
            throw $this->createNotFoundException('Élément invalide');
        }

        if ($type === 'movie') {
            $movie = $entityManager->getRepository(Movie::class)->find($itemId);
            if ($movie) {
                $watchList->removeMovie($movie);
            }
        }

        if ($type === 'series') {
            $series = $entityManager->getRepository(Series::class)->find($itemId);
            if ($series) {
                $watchList->removeSeries($series);
            }
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_watch_list_show', ['id' => $watchList->getId()]);
    }

}
