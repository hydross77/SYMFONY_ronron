<?php

namespace App\Controller;

use App\Entity\Announce;
use App\Entity\Cat;
use App\Form\AnnounceType;
use App\Form\CatType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AnnounceController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/announce', name: 'app_announce')]
    public function index(): Response
    {
        return $this->render('announce/index.html.twig', [
            'controller_name' => 'AnnounceController',
        ]);
    }

    #[Route('/announce/new', name: 'app_announce_new')]
    public function new(Request $request, SessionInterface $session): Response
    {
        $cat = new Cat();
        $formCat = $this->createForm(CatType::class, $cat);
        $formCat->handleRequest($request);

        if ($formCat->isSubmitted() && $formCat->isValid()) {
            $cat = $formCat->getData();

            $user = $this->getUser();
            // Associer l'utilisateur courant à l'annonce
            $cat->setUser($user);

            $this->entityManager->persist($user);
            $this->entityManager->persist($cat);
            $this->entityManager->flush();

            // Stocker l'ID du chat dans la session
            $session->set('cat_id', $cat->getId());

            return $this->redirectToRoute('app_announce_create');
        }

        return $this->render('announce/new.html.twig', [
            'formCat' => $formCat->createView(),
        ]);
    }

    #[Route('/announce/create', name: 'app_announce_create')]
    public function create(Request $request, SessionInterface $session): Response
    {
        $catId = $session->get('cat_id');
        $user = $this->getUser();

        $announce = new Announce();
        $announce->setUser($user);
        $announce->setCat($this->entityManager->getRepository(Cat::class)->find($catId));

        $form = $this->createForm(AnnounceType::class, $announce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $announce = $form->getData();

            $user = $this->getUser();

            // Associer l'utilisateur courant à l'annonce
            $announce->setUser($user);

            $this->entityManager->persist($user);
            $this->entityManager->persist($announce);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_announce');
        }

        return $this->render('announce/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/announce/{id}', name: 'app_announce_show')]
    public function show($id): Response
    {
        $announce = $this->entityManager->getRepository(Announce::class)->find($id);
        $cat = $announce->getCat();

        return $this->render('announce/show.html.twig', [
            'announce' => $announce,
            'cat' => $cat,
        ]);
    }


}
