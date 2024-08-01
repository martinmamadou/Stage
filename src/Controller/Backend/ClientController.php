<?php

namespace App\Controller\Backend;

use App\Entity\Client;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/client', 'admin.client')]
class ClientController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ClientRepository $clientRepo,

    )
    {

    }
    #[Route('', name: '.index')]
    public function index(): Response
    {
        return $this->render('Backend/Client/index.html.twig', [
            'clients' => $this->clientRepo->findAll()
        ]);
    }

    #[Route('/create','.create', methods:['GET','POST'])]
    public function create(Request $request):Response|RedirectResponse
    {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid())
        {
            $this->em->persist($client);
            $this->em->flush();

            $this->addFlash('succes', 'client ajouter avec succès');
            return $this->redirectToRoute('admin.client.index');
        }
        return $this->render('Backend/Client/create.html.twig', [
            'clients' => $this->clientRepo->findAll(),
            'form' => $form
        ]);
    }

    #[Route('/{id}/edit','.edit', methods:['GET','POST'])]
    public function edit(?Client $client,Request $request):Response|RedirectResponse
    {
        if (!$client) {
            $this->addFlash('danger', 'Client introuvable.');

            return $this->redirectToRoute('admin.client.index');
        }

        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($client);
            $this->em->flush();

            $this->addFlash('success', 'Client mis à jour.');

            return $this->redirectToRoute('admin.client.index');
        }

        return $this->render('Backend/Client/edit.html.twig', [
            'form' => $form,
        ]);
}

#[Route('/{id}/delete', name: '.delete', methods: ['POST'])]
    public function delete(?Client $client, Request $request): RedirectResponse
    {
        if (!$client) {
            $this->addFlash('danger', 'Client introuvable.');

            return $this->redirectToRoute('admin.client.index');
        }

        if ($this->isCsrfTokenValid('delete' . $client->getId(), $request->request->get('token'))) {
            $this->em->remove($client);
            $this->em->flush();

            $this->addFlash('success', 'Client supprimé.');
        } else {
            $this->addFlash('danger', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('admin.client.index');
    }
}