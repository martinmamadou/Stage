<?php

namespace App\Controller\Backend;

use App\Entity\Event;
use App\Form\EventType;
use App\Repository\EventRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('admin/event','admin.event')]
class EventController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EventRepository $eventRepo
    )
    {
        
    }
    #[Route('', name: '.index')]
    public function index(): Response
    {
        return $this->render('backend/event/index.html.twig', [
            'controller_name' => 'EventController',
        ]);
    }

    #[Route('/create', '.create', methods:['GET','POST'])]
    public function create(Request $request):Response|RedirectResponse
    {
        $event = new Event();
        $form = $this->createForm(EventType::class,$event);
        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid()){
            $this->em->persist($event);
            $this->em->flush();

            $this->addFlash('success','ajout d\'evenement reussi');
            return $this->redirectToRoute('app_test');
        }


        return $this->render('Backend/Event/create.html.twig', [
            'form' => $form
        ]);
    }
}
