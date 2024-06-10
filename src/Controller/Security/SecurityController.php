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
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SecurityController extends AbstractController
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
        private readonly EntityManagerInterface $em,
        private UserRepository $userRepo,
    ) {
    }



    #[Route('/login', name: 'app.login', methods: ['GET', 'POST'])]
    public function login(AuthenticationUtils $authUtils): Response
    {
        // Récupérer les erreurs de connexion
        $error = $authUtils->getLastAuthenticationError();
        // Récupérer le dernier nom d'utilisateur saisi
        $lastUsername = $authUtils->getLastUsername();

        // Rendre la vue du formulaire de connexion
        return $this->render('login.html.twig', [
            'error' => $error,
            'lastUsername' => $lastUsername
        ]);
    }



    #[Route('/password', name: 'app.password', methods: ['GET', 'POST'])]
    public function password(?User $user, Request $request)
    {


        $user = $this->getUser();
        if (!$user->isFirstlog()) {
            return $this->redirectToRoute('app.home');
        }

        $form = $this->createForm(UserType::class, $user, ['firstLogin' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user
                ->setPassword($this->hasher->hashPassword($user, $form->get('password')->getData()))
                ->setFirstlog(false);


            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', 'Utilisateur mis à jour.');

            return $this->redirectToRoute('app.home');
        }

        return $this->render('password.html.twig', [
            'form' => $form,
        ]);
    }
}
