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
        $startDate = $request->request->get('start_date');
        $endDate = $request->request->get('end_date');

        $movements = $employeeMovementRepository->findByClientAndDateRange($clientId, $startDate, $endDate);

        if (empty($movements)) {
            $this->addFlash('error', 'Aucun déplacement prévu pour ce client dans cette période de temps donnée');
            return $this->redirectToRoute('app.home');
        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Définir les en-têtes
        $sheet->mergeCells('A1:D1');
        $sheet->setCellValue('A1', 'Mai - S20 (TE => Rafik, Julien, Pierre, Laurie)');
        $sheet->getStyle('A1')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->setCellValue('A2', '');
        $sheet->setCellValue('B2', '13');
        $sheet->setCellValue('C2', '14');
        $sheet->setCellValue('D2', '15');

        // Exemple d'ajout de données avec des styles
        $row = 3;
        foreach ($movements as $movement) {
            $sheet->setCellValue('A' . $row, $movement->getUser()->getLastname());
            $sheet->setCellValue('B' . $row, ''); // Initialement vide
            $sheet->setCellValue('C' . $row, ''); // Initialement vide
            $sheet->setCellValue('D' . $row, ''); // Initialement vide

            // Supposons que $movement->getDate() retourne l'objet DateTime
            $day = (int)$movement->getDate()->format('d');
            if ($day == 13) {
                $sheet->setCellValue('B' . $row, $movement->getMoveDescription());
                $sheet->getStyle('B' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);
            } elseif ($day == 14) {
                $sheet->setCellValue('C' . $row, $movement->getMoveDescription());
                $sheet->getStyle('C' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);
            } elseif ($day == 15) {
                $sheet->setCellValue('D' . $row, $movement->getMoveDescription());
                $sheet->getStyle('D' . $row)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB(Color::COLOR_YELLOW);
            }
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
        $sheet->getStyle('A1:D' . ($row - 1))->applyFromArray($styleArray);

        // Agrandir toutes les cellules
        for ($col = ord('A'); $col <= ord('D'); $col++) {
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
