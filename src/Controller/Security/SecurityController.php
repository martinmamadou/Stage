<?php

namespace App\Controller\Security;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
    ) {}

    #[Route('/register', name: 'app.register', methods: ['GET', 'POST'])]
    public function register(Request $request, EntityManagerInterface $em): Response|RedirectResponse
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->hasher->hashPassword($user, $form->get('password')->getData()));

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Compte créer avec succès.');

            return $this->redirectToRoute('app.login');
        }

        return $this->render('register.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/login', name: 'app.login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authUtils): Response
    {
        return $this->render('login.html.twig', [
            'error' => $authUtils->getLastAuthenticationError(),
            'lastUsername' => $authUtils->getLastUsername(),
        ]);
    }
}
