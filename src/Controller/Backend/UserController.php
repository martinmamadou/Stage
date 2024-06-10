<?php

namespace App\Controller\Backend;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('', name: 'admin.users')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepo,
        private readonly EntityManagerInterface $em,
        private UserPasswordHasherInterface $hasher,
    ) {
    }

    #[Route('/index', name: '.index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('Backend/User/index.html.twig', [
            'users' => $this->userRepo->findAll(),
        ]);
    }



    #[Route('/create', name: '.create', methods: ['GET', 'POST'])]
    public function register(Request $request, EntityManagerInterface $em): Response|RedirectResponse
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {

            $user
                ->setFirstlog(true)
                ->setPassword($this->hasher->hashPassword($user, $form->get('password')->getData()));

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Compte créer avec succès.');

            return $this->redirectToRoute('app.login');
        }

        return $this->render('Backend/User/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: '.edit', methods: ['GET', 'POST'])]
    public function edit(?User $user, Request $request): Response|RedirectResponse
    {
        if (!$user) {
            $this->addFlash('danger', 'Utilisateur introuvable.');

            return $this->redirectToRoute('admin.users.index');
        }

        $form = $this->createForm(UserType::class, $user, ['isAdmin' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', 'Utilisateur mis à jour.');

            return $this->redirectToRoute('admin.users.index');
        }

        return $this->render('Backend/User/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: '.delete', methods: ['POST'])]
    public function delete(User $user, Request $request): RedirectResponse
    {
        if (!$user) {
            $this->addFlash('danger', 'Utilisateur introuvable.');

            return $this->redirectToRoute('admin.users.index');
        }

        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('token'))) {
            $this->em->remove($user);
            $this->em->flush();

            $this->addFlash('success', 'Utilisateur supprimé.');
        } else {
            $this->addFlash('danger', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('admin.users.index');
    }
}
