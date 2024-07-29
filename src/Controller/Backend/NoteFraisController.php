<?php

namespace App\Controller\Backend;

use App\Entity\NoteFrais;
use App\Form\NoteFraisType;
use App\Repository\NoteFraisRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/NoteFrais')]
class NoteFraisController extends AbstractController
{
    public function __construct(
        private readonly NoteFraisRepository $deviRepo
    )
    {
        
    }

    #[Route('/', name: 'app_NoteFrais_index', methods: ['GET'])]
    public function index(): Response
    {
        $currentMonth = (new \DateTime())->format('m');

        return $this->render('NoteFrais/index.html.twig', [
            'NoteFrais' => $this->deviRepo->findAll(),
            'currentMonth' => $currentMonth,
        ]);
    }

    #[Route('/new', name: 'app_NoteFrais_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $devi = new NoteFrais();
        
        $form = $this->createForm(NoteFraisType::class, $devi);
        $form->handleRequest($request);
       
        if ($form->isSubmitted() && $form->isValid()) {
            
            $entityManager->persist($devi);
            
            $entityManager->flush();

            return $this->redirectToRoute('app_NoteFrais_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('NoteFrais/new.html.twig', [
            'devi' => $devi,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_NoteFrais_show', methods: ['GET'])]
    public function show(NoteFrais $devi): Response
    {
        return $this->render('NoteFrais/show.html.twig', [
            'devi' => $devi,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_NoteFrais_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, NoteFrais $devi, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(NoteFraisType::class, $devi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_NoteFrais_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('NoteFrais/edit.html.twig', [
            'devi' => $devi,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_NoteFrais_delete', methods: ['POST'])]
    public function delete(Request $request, NoteFrais $devi, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$devi->getId(), $request->request->get('_token'))) {
            $entityManager->remove($devi);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_NoteFrais_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/export/excel', name: 'app_NoteFrais_export_excel', methods: ['GET'])]
    public function exportExcel(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Définir les en-têtes
        $headers = ['Id', 'Titre', 'Categorie', 'PrixHt', 'Quantite', 'Km', 'PrixKm', 'TotalTTC'];
        $sheet->fromArray($headers, null, 'A1');

        // Récupérer les NoteFrais
        $NoteFrais = $this->deviRepo->findAll();

        // Ajouter les données des NoteFrais
        $row = 2; // Démarrer à la ligne 2 car la ligne 1 est pour les en-têtes
        foreach ($NoteFrais as $devi) {
            $sheet->setCellValue('A' . $row, $devi->getId());
            $sheet->setCellValue('B' . $row, $devi->getTitre());
            $sheet->setCellValue('C' . $row, $devi->getCategorie());
            $sheet->setCellValue('D' . $row, $devi->getPrixHt());
            $sheet->setCellValue('E' . $row, $devi->getQuantite());
            $sheet->setCellValue('F' . $row, $devi->getKm());
            $sheet->setCellValue('G' . $row, $devi->getPrixKm());
            $sheet->setCellValue('H' . $row, $devi->getTotalTTC());
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $dispositionHeader = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'NoteFrais.xlsx'
        );

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', $dispositionHeader);

        return $response;
    }
}
