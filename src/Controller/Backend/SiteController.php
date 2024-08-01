<?php

namespace App\Controller\Backend;

use App\Entity\Site;
use App\Form\SiteType;
use Doctrine\ORM\Mapping\Entity;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/site', name: 'admin.site')]
class SiteController extends AbstractController
{
    public function __construct(
        private readonly SiteRepository $siteRepo,
        private readonly EntityManagerInterface $em
    )
    {
        
    }
    #[Route('', name: '.index', methods:['GET'])]
    public function index(): Response
    {
        
        return $this->render('backend/site/index.html.twig', [
            'sites' => $this->siteRepo->findAll(),
        ]);
    }

    #[Route('/create','.create', methods:['GET','POST'])]
    public function create(Request $request):Response|RedirectResponse
    {
        $site = new Site();
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);
        if($form->isSubmitted()&&$form->isValid())
        {
            $this->em->persist($site);
            $this->em->flush();

            $this->addFlash('succes', 'site ajouter avec succès');
            return $this->redirectToRoute('admin.client.index');
        }
        return $this->render('Backend/Site/create.html.twig', [
            'sites' => $this->siteRepo->findAll(),
            'form' => $form
        ]);
    }
    #[Route('/{id}/edit','.edit', methods:['GET','POST'])]
    public function edit(?Site $site,Request $request):Response|RedirectResponse
    {
        if (!$site) {
            $this->addFlash('danger', 'site introuvable.');

            return $this->redirectToRoute('admin.site.index');
        }

        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($site);
            $this->em->flush();

            $this->addFlash('success', 'site mis à jour.');

            return $this->redirectToRoute('admin.site.index');
        }

        return $this->render('Backend/Site/edit.html.twig', [
            'form' => $form,
        ]);
}

#[Route('/{id}/delete', name: '.delete', methods: ['POST'])]
    public function delete(?Site $site, Request $request): RedirectResponse
    {
        if (!$site) {
            $this->addFlash('danger', 'site introuvable.');

            return $this->redirectToRoute('admin.site.index');
        }

        if ($this->isCsrfTokenValid('delete' . $site->getId(), $request->request->get('token'))) {
            $this->em->remove($site);
            $this->em->flush();

            $this->addFlash('success', 'site supprimé.');
        } else {
            $this->addFlash('danger', 'Token CSRF invalide.');
        }

        return $this->redirectToRoute('admin.site.index');
    }
}
