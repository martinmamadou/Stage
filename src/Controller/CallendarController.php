<?php

namespace App\Controller;

use App\Repository\EventRepository;
use App\Repository\UserRepository;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CallendarController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepo,
        private readonly EventRepository $eventRepo
    ) {

    }

    #[Route('', name: 'app.home', methods:['GET'])]
    public function index(): Response
    {
        $user= $this->getUser();
        if(!$user){
            return $this->redirectToRoute('app.login');
        }

        $events = $this->eventRepo->findFutureEvents();
        
        foreach($events as $event){
            $rdvs[] = [
                'id' => $event->getId(),
                'start' => $event->getStartDate()->format('Y-m-d H:i:s'),
                'end' => $event->getEndDate()->format('Y-m-d H:i:s'),
                'title' => $event->getTitre(),
                'backgroundColor' => $event->getColor(),
                'description' => $event->getDescription(),
                'user' => $event->getUser(),
            ];
            
        }

        $data = empty($rdvs) ? '[]' : json_encode($rdvs);        
        return $this->render('Callendar/index.html.twig',['data'=>$data,
        'users' => $this->userRepo->findAll(),
        'events' => $events
        ]
        );
    }
}
