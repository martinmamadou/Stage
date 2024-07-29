<?php

namespace App\Controller\Frontend;

use App\Entity\NoteFrais;
use App\Form\NoteFraisType;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use App\Repository\NoteFraisRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/NoteFrais','NoteFrais')]
class NoteFraisController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepo,
        private readonly NoteFraisRepository $NoteFraisRepo,
        private readonly ClientRepository $clientRepo
    )
    {
        
    }
    #[Route('/', name: '.index' ,methods:['GET'])]
    public function index(): Response
    {
        return $this->render('Frontend/NoteFrais/index.html.twig', [
            'users' => $this->userRepo->findAll(),
            'NoteFrais'=>$this->NoteFraisRepo->findAll(),
            'clients'=> $this->clientRepo->findAll()
        ]);
    }
    
    #[Route('/showclient', name: '.showClient', methods: ['GET'])]
    public function showclient(): Response
    {
    
    $client = $this->clientRepo->findAll();
    
    if (!$client) {
        throw $this->createNotFoundException('Utilisateur non trouvé');
    }

    // Utiliser la méthode findNoteFraisForCurrentMonth du repository
    $NoteFrais = $this->NoteFraisRepo->findClientNoteFraisForCurrentMonth();


    return $this->render('Frontend/NoteFrais/showClient.html.twig', [
        'client' => $client,
        'NoteFrais' => $NoteFrais,
        
    ]);
}
    #[Route('/show/{id}', name: '.show', methods: ['GET'])]
public function show(int $id): Response
{
    $user = $this->userRepo->find($id);
    $totalTTC=0;
    $totalTaxe =0;
    if (!$user) {
        throw $this->createNotFoundException('Utilisateur non trouvé');
    }

    // Utiliser la méthode findNoteFraisForCurrentMonth du repository
    $NoteFrais = $this->NoteFraisRepo->findNoteFraisForCurrentMonth($id);
    foreach($NoteFrais as $devi ){
        $totalTTC += $devi->getTotalTTC();
        $totalTaxe += $devi->getTotalTaxe();
    }

    return $this->render('Frontend/NoteFrais/show.html.twig', [
        'user' => $user,
        'NoteFrais' => $NoteFrais,
        'total' => $totalTTC,
        'taxe' => $totalTaxe
    ]);
}

    #[Route('/new', name: '.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $devi = new NoteFrais();
        $form = $this->createForm(NoteFraisType::class, $devi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($devi);
            $entityManager->flush();

            return $this->redirectToRoute('NoteFrais.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('NoteFrais/new.html.twig', [
            'devi' => $devi,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/export/excel', name: 'app_NoteFrais_export_excel', methods: ['GET'])]
    public function exportExcel(Request $request): StreamedResponse
    {
        $userId = $request->request->get('user');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        // Définir les en-têtes
        $headers = ['Id', 'Titre', 'Categorie', 'PrixHt', 'Quantite', 'Km', 'PrixKm', 'TotalTTC'];
        $sheet->fromArray($headers, null, 'A1');
    
        // Récupérer les NoteFrais associés à l'utilisateur spécifié
        $NoteFrais = $this->NoteFraisRepo->findNoteFraisByUser($userId);
        dd($NoteFrais); // Vérifiez ici ce qui est renvoyé pour le debugging
    
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