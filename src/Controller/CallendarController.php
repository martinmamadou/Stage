<?php
namespace App\Controller;

use App\Entity\Event;
use App\Form\EventType;
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
use Symfony\Component\HttpFoundation\RedirectResponse;
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
                $employeeMovement->setEnd($event->getEndDate());
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

    #[Route('/event/create', 'event.create', methods:['GET','POST'])]
    public function create(Request $request):Response|RedirectResponse
    {
        $event = new Event();
        $form = $this->createForm(EventType::class,$event);
        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid()){

            $dateEvenement = $event->getStartDate(); // Supposons que la date soit stockée dans l'objet Event
            $dateActuelle = new \DateTime();

        if ($dateEvenement < $dateActuelle) {
            $this->addFlash('error', 'La date de l\'événement ne peut pas être antérieure à la date actuelle');
            return $this->redirectToRoute('admin.event.create'); // Rediriger vers le formulaire de création avec un message d'erreur
        }

            $this->em->persist($event);
            $this->em->flush();

            $this->addFlash('success','ajout d\'evenement reussi');
            return $this->redirectToRoute('app.home');
        }


        return $this->render('Backend/Event/create.html.twig', [
            'form' => $form,
            'events'=>$this->eventRepo->findFutureEvents()
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
    $rowOffset = 1;

    // Grouper les mouvements par site
    $movementsBySite = [];
    foreach ($movements as $movement) {
        $siteName = $movement->getSite()->getName();
        if (!isset($movementsBySite[$siteName])) {
            $movementsBySite[$siteName] = [];
        }
        $movementsBySite[$siteName][] = $movement;
    }

    foreach ($movementsBySite as $siteName => $siteMovements) {
        // Ajouter le titre du site
        $sheet->mergeCells('A' . $rowOffset . ':D' . $rowOffset);
        $sheet->setCellValue('A' . $rowOffset, "Site: $siteName");
        $sheet->getStyle('A' . $rowOffset)->getFont()->setBold(true);
        $sheet->getStyle('A' . $rowOffset)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Générer les en-têtes
        $rowOffset++;
        $sheet->setCellValue('A' . $rowOffset, 'Nom');
        $sheet->setCellValue('B' . $rowOffset, 'Semaine');
        $sheet->setCellValue('C' . $rowOffset, 'Description Mission');
        $sheet->setCellValue('D' . $rowOffset, 'Groupe');

        // Ajouter les données des mouvements
        $rowOffset++;
        foreach ($siteMovements as $movement) {
            $movementStartDate = $movement->getDate();
            $movementEndDate = $movement->getEnd();

            $startWeek = (int)$movementStartDate->format('W');
            $endWeek = (int)$movementEndDate->format('W');
            $weekRange = $startWeek === $endWeek ? "Semaine $startWeek" : "Semaine $startWeek - $endWeek";

            $sheet->setCellValue('A' . $rowOffset, $movement->getUser()->getLastname());
            $sheet->setCellValue('B' . $rowOffset, $weekRange); // Plage de semaines
            $sheet->setCellValue('C' . $rowOffset, $movement->getMoveDescription());
            $sheet->setCellValue('D' . $rowOffset, $movement->getGroupe());

            $rowOffset++;
        }

        // Ajouter un espace entre les tableaux des sites
        $rowOffset += 2;
    }

    // Agrandir la taille des cellules
    foreach (range('A', 'D') as $columnID) {
        $sheet->getColumnDimension($columnID)->setWidth(30); // Ajuster la largeur des colonnes
    }

    for ($rowIndex = 1; $rowIndex <= $rowOffset; $rowIndex++) {
        $sheet->getRowDimension($rowIndex)->setRowHeight(30); // Ajuster la hauteur des lignes
    }

    // Génération du fichier Excel
    $writer = new Xlsx($spreadsheet);
    $fileName = 'planning_' . date('Y-m-d') . '.xlsx';
    $tempFile = tempnam(sys_get_temp_dir(), $fileName);
    $writer->save($tempFile);

    return $this->file($tempFile, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);
}


}

