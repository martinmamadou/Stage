<?php

namespace App\Controller\Frontend;

use App\Entity\Devis;
use App\Form\DevisType;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use App\Repository\DevisRepository;
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

#[Route('/devis','devis')]
class DevisController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepo,
        private readonly DevisRepository $devisRepo,
        private readonly ClientRepository $clientRepo
    )
    {
        
    }
    #[Route('/', name: '.index' ,methods:['GET'])]
    public function index(): Response
    {
        return $this->render('Frontend/Devis/index.html.twig', [
            'users' => $this->userRepo->findAll(),
            'devis'=>$this->devisRepo->findAll(),
            'clients'=> $this->clientRepo->findAll()
        ]);
    }
    
    #[Route('/showclient/{id}', name: '.showClient', methods: ['GET'])]
    public function showclient(int $id): Response
    {
    $client = $this->clientRepo->find($id);
    
    if (!$client) {
        throw $this->createNotFoundException('Utilisateur non trouvé');
    }

    // Utiliser la méthode findDevisForCurrentMonth du repository
    $devis = $this->devisRepo->findClientDevisForCurrentMonth($id);


    return $this->render('Frontend/Devis/showClient.html.twig', [
        'client' => $client,
        'devis' => $devis,
        
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

    // Utiliser la méthode findDevisForCurrentMonth du repository
    $devis = $this->devisRepo->findDevisForCurrentMonth($id);
    foreach($devis as $devi ){
        $totalTTC += $devi->getTotalTTC();
        $totalTaxe += $devi->getTotalTaxe();
    }

    return $this->render('Frontend/Devis/show.html.twig', [
        'user' => $user,
        'devis' => $devis,
        'total' => $totalTTC,
        'taxe' => $totalTaxe
    ]);
}

    #[Route('/new', name: '.new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $devi = new Devis();
        $form = $this->createForm(DevisType::class, $devi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($devi);
            $entityManager->flush();

            return $this->redirectToRoute('app_devis_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('devis/new.html.twig', [
            'devi' => $devi,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/export/excel', name: 'app_devis_export_excel', methods: ['GET'])]
    public function exportExcel(Request $request): StreamedResponse
    {
        $userId = $request->request->get('user');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        // Définir les en-têtes
        $headers = ['Id', 'Titre', 'Categorie', 'PrixHt', 'Quantite', 'Km', 'PrixKm', 'TotalTTC'];
        $sheet->fromArray($headers, null, 'A1');
    
        // Récupérer les devis associés à l'utilisateur spécifié
        $devis = $this->devisRepo->findDevisByUser($userId);
        dd($devis); // Vérifiez ici ce qui est renvoyé pour le debugging
    
        // Ajouter les données des devis
        $row = 2; // Démarrer à la ligne 2 car la ligne 1 est pour les en-têtes
        foreach ($devis as $devi) {
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
            'devis.xlsx'
        );
    
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', $dispositionHeader);
    
        return $response;
    }
    
}