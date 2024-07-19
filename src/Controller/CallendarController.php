<?php
namespace App\Controller;

use App\Entity\EmployeeMovement;
use App\Repository\UserRepository;
use App\Repository\EventRepository;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EmployeeMovementRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CallendarController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepo,
        private readonly EventRepository $eventRepo,
        private readonly ClientRepository $clientRepo,
        private readonly EntityManagerInterface $em
    ) {}

    #[Route('', name: 'app.home', methods:['GET'])]
    public function index(): Response
    {
        
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app.login');
        }

        $events = $this->eventRepo->findFutureEvents();

        if (empty($events)) {
            
            return $this->render('Callendar/index.html.twig', [
                'data' => '[]',
                'users' => $this->userRepo->findAll(),
                'events' => $events,
                'clients' => $this->clientRepo->findAll(),
            ]);
        }

        foreach ($events as $event) {
            $existingMovement = $this->em->getRepository(EmployeeMovement::class)->findOneBy(['event' => $event]);

            if (!$existingMovement) {
                $employeeMovement = new EmployeeMovement();
                $employeeMovement->setEvent($event);
                $employeeMovement->setDate($event->getStartDate());
                $employeeMovement->setMoveDescription($event->getDescription());
                $employeeMovement->setClient($event->getClient());
                $employeeMovement->setUser($event->getUser());
                $employeeMovement->setTitre($event->getTitre());
                $employeeMovement->setSite($event->getSite());
                $employeeMovement->setGroupe($event->getGroupe());
                // Autres propriétés de l'EmployeeMovement...

                $this->em->persist($employeeMovement);
            }
        }

        $this->em->flush();

        $data = [];
        foreach ($events as $event) {
            $data[] = [
                'id' => $event->getId(),
                'start' => $event->getStartDate()->format('Y-m-d H:i:s'),
                'end' => $event->getEndDate()->format('Y-m-d H:i:s'),
                'title' => $event->getTitre(),
                'backgroundColor' => $event->getUser()->getColor(),
                'description' => $event->getDescription(),
                'user' => $event->getUser(),
                'client' => $event->getClient(),
                'site' => $event->getSite(),
            ];
        }

        $data = json_encode($data);

        return $this->render('Callendar/index.html.twig', [
            'data' => $data,
            'users' => $this->userRepo->findAll(),
            'events' => $events,
            'clients' => $this->clientRepo->findAll(),
        ]);
    }

    #[Route('/planning/generate', name: 'planning_generate', methods: ['POST'])]
    public function generate(Request $request, EmployeeMovementRepository $employeeMovementRepository): Response
    {
        $clientId = $request->request->get('client_id');
        $startDate = new \DateTime($request->request->get('start_date'));
        $endDate = new \DateTime($request->request->get('end_date'));

        $movements = $employeeMovementRepository->findByClientAndDateRange($clientId, $startDate, $endDate);

        if (empty($movements)) {
            $this->addFlash('error', 'Aucun déplacement prévu pour ce client dans cette période de temps donnée');
            return $this->redirectToRoute('app.home');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Définir les en-têtes
        $weekNumber = $startDate->format('W');
        $sheet->mergeCells('A1:' . chr(66 + $endDate->diff($startDate)->days) . '1');
        $sheet->setCellValue('A1', "Semaine $weekNumber");
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Générer dynamiquement les en-têtes des colonnes pour chaque jour de la période
        $sheet->setCellValue('A2', 'Nom');
        $col = 66; // ASCII value for 'B'
        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            $sheet->setCellValue(chr($col) . '2', $currentDate->format('d'));
            $currentDate->modify('+1 day');
            $col++;
        }

        // Ajouter les données avec des styles
        $row = 3;
        foreach ($movements as $movement) {
            $sheet->setCellValue('A' . $row, $movement->getUser()->getLastname());

            // Initialiser les colonnes dynamiques
            $col = 66; // ASCII value for 'B'
            $currentDate = clone $startDate;
            while ($currentDate <= $endDate) {
                $sheet->setCellValue(chr($col) . $row, ''); // Initialement vide
                $col++;
                $currentDate->modify('+1 day');
            }

            // Remplir les données selon les dates de mouvement
            $day = (int)$movement->getDate()->format('d');
            $colIndex = 66 + ($movement->getDate()->diff($startDate)->days); // Calculer la colonne en fonction de la différence de jours

            // Ajouter la description du mouvement et le groupe
            $cellValue = $movement->getMoveDescription() . ' (Groupe ' . $movement->getGroupe() . ')';
            $sheet->setCellValue(chr($colIndex) . $row, $cellValue);
            $sheet->getStyle(chr($colIndex) . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);

            $row++;
        }

        // Appliquer les bordures et autres styles
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A1:' . chr(66 + $endDate->diff($startDate)->days) . ($row - 1))->applyFromArray($styleArray);

        // Agrandir toutes les cellules
        for ($col = 65; $col <= 66 + $endDate->diff($startDate)->days; $col++) {
            $sheet->getColumnDimension(chr($col))->setWidth(25);
        }
        for ($rowIndex = 1; $rowIndex <= $row - 1; $rowIndex++) {
            $sheet->getRowDimension($rowIndex)->setRowHeight(25);
        }

        // Génération du fichier Excel
        $writer = new Xlsx($spreadsheet);
        $fileName = 'planning_' . date('Y-m-d') . '.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return $this->file($tempFile, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }


    

}

