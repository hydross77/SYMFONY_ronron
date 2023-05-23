<?php

namespace App\Controller;

use App\Entity\Announce;
use App\Form\SearchForm;
use App\Repository\AnnounceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;


class HomeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Affiche la page d'accueil avec la barre de recherche multi-filtre
     * @Route("/", name="app_home")
     */
    public function bdr(AnnounceRepository $repository, Request $request): Response
    {

        $announces = $this->entityManager->getRepository(Announce::class);


        return $this->render('home/index.html.twig', [
            'announces'=>$announces,
        ]);
    }
}
