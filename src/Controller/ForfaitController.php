<?php

namespace App\Controller;

use App\Entity\Forfait;
use App\Form\ForfaitType;
use App\Repository\ForfaitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/forfait')]
class ForfaitController extends AbstractController
{
    #[Route('/', name: 'app_forfait_index', methods: ['GET'])]
    public function index(ForfaitRepository $forfaitRepository): Response
    {
        return $this->render('forfait/index.html.twig', [
            'forfaits' => $forfaitRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_forfait_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $forfait = new Forfait();
        $form = $this->createForm(ForfaitType::class, $forfait);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($forfait);
            $entityManager->flush();

            return $this->redirectToRoute('app_forfait_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('forfait/new.html.twig', [
            'forfait' => $forfait,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_forfait_show', methods: ['GET'])]
    public function show(Forfait $forfait): Response
    {
        return $this->render('forfait/show.html.twig', [
            'forfait' => $forfait,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_forfait_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Forfait $forfait, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ForfaitType::class, $forfait);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_forfait_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('forfait/edit.html.twig', [
            'forfait' => $forfait,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_forfait_delete', methods: ['POST'])]
    public function delete(Request $request, Forfait $forfait, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$forfait->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($forfait);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_forfait_index', [], Response::HTTP_SEE_OTHER);
    }
}
