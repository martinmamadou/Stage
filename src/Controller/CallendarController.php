<?php

namespace App\Controller;

use App\Repository\EventRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CallendarController extends AbstractController
{
    #[Route('/test', name: 'app_test')]
    public function index(EventRepository $eventrepo): Response
    {
        $events = $eventrepo->findFutureEvents();
        
        foreach($events as $event){
            $rdvs[] = [
                'id' => $event->getId(),
                'start' => $event->getStartDate()->format('Y-m-d H:i:s'),
                'end' => $event->getEndDate()->format('Y-m-d H:i:s'),
                'title' => $event->getTitre(),
                'backgroundColor' => $event->getColor(),
                'description' => $event->getDescription(),
            ];
        }

        $data = json_encode($rdvs);
        
        return $this->render('Callendar/index.html.twig',compact('data')
        );
    }
}
