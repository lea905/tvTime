<?php

namespace App\Controller;

use App\Entity\WatchList;
use App\Form\WatchListType;
use App\Repository\WatchListRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/watch/list')]
final class WatchListController extends AbstractController
{
    #[Route(name: 'app_watch_list_index', methods: ['GET'])]
    public function index(WatchListRepository $watchListRepository): Response
    {
        return $this->render('watch_list/index.html.twig', [
            'watch_lists' => $watchListRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_watch_list_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $watchList = new WatchList();
        $watchList->setIdUser($this->getUser()->getId());
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
}
