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
            'events' => $this->eventRepo->findAll()
        ]);
    }

    #[Route('/create', '.create', methods:['GET','POST'])]
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

    #[Route('/{id}/edit','.edit', methods:['GET','POST'])]
    public function edit(?Event $event,Request $request):Response|RedirectResponse
    {
        if (!$event) {
            $this->addFlash('danger', 'evenement introuvable.');

            return $this->redirectToRoute('admin.event.index');
        }

        $form = $this->createForm(eventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($event);
            $this->em->flush();

            $this->addFlash('success', 'evenement mis à jour.');

            return $this->redirectToRoute('admin.event.index');
        }

        return $this->render('Backend/Event/edit.html.twig', [
            'form' => $form,
        ]);
}

#[Route('/{id}/delete', name: '.delete', methods: ['POST'])]
    public function delete(?Event $event, Request $request): RedirectResponse
    {
        if (!$event) {
            $this->addFlash('danger', 'evenement introuvable.');

            return $this->redirectToRoute('admin.event.index');
        }

        if ($this->isCsrfTokenValid('delete' . $event->getId(), $request->request->get('token'))) {
            $this->em->remove($event);
            $this->em->flush();

            $this->addFlash('success', 'evenement supprimé.');
        } else {
            $this->addFlash('danger', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('admin.event.index');
    }
}
