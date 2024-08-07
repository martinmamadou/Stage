<?php

namespace App\Controller;

use App\Entity\Taxe;
use App\Form\TaxeType;
use App\Repository\TaxeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


class TaxeController extends AbstractController
{
    #[Route('/admin/taxe', name: 'admin.taxe.index', methods: ['GET'])]
    public function index(TaxeRepository $taxeRepository): Response
    {
        return $this->render('taxe/index.html.twig', [
            'taxes' => $taxeRepository->findAll(),
        ]);
    }

    #[Route('admin/taxe/new', name: 'admin.taxe.create', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $taxe = new Taxe();
        $form = $this->createForm(TaxeType::class, $taxe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($taxe);
            $entityManager->flush();

            return $this->redirectToRoute('app_taxe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('taxe/new.html.twig', [
            'taxe' => $taxe,
            'form' => $form,
        ]);
    }

    #[Route('/admin/taxe/{id}', name: 'app_taxe_show', methods: ['GET'])]
    public function show(Taxe $taxe): Response
    {
        return $this->render('taxe/show.html.twig', [
            'taxe' => $taxe,
        ]);
    }

    #[Route('/admin/taxe/{id}/edit', name: 'admin.taxe.edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Taxe $taxe, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TaxeType::class, $taxe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('taxe.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('taxe/edit.html.twig', [
            'taxe' => $taxe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_taxe_delete', methods: ['POST'])]
    public function delete(Request $request, Taxe $taxe, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$taxe->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($taxe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin.taxe.index', [], Response::HTTP_SEE_OTHER);
    }
}
