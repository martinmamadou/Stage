<?php
namespace App\Controller;

use App\Entity\EmployeeMovement;
use App\Repository\UserRepository;
use App\Repository\EventRepository;
use App\Repository\ClientRepository;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\EmployeeMovementRepository;
use Doctrine\ORM\EntityManagerInterface;
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
                'client' => $event->getClient()
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
        $sheet->setCellValue('C1', 'Client');
        $sheet->setCellValue('B1', 'Date');
        $sheet->setCellValue('A1', 'Employé');
        $sheet->setCellValue('D1', 'Déplacement');
        $sheet->getColumnDimension('A')->setWidth(20);

        $row = 2;
        foreach ($movements as $movement) {
            $sheet->setCellValue('C' . $row, $movement->getClient()->getName());
            $sheet->setCellValue('B' . $row, $movement->getDate()->format('Y-m-d'));
            $sheet->setCellValue('A' . $row, $movement->getUser()->getLastname());
            $sheet->setCellValue('D' . $row, $movement->getMoveDescription());
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = $movement->getTitre().'.xlsx';
        $tempFile = tempnam(sys_get_temp_dir(), $fileName);
        $writer->save($tempFile);

        return $this->file($tempFile, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
    }
}
