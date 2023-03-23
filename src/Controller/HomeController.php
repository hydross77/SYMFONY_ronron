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



        $searchAnnounce = $this->createForm(SearchForm::class, null);
        // crée le formulaire configuré dans le dossier FORM
        $searchAnnounce->handleRequest($request);
        // traite les données du formulaire


        if ($searchAnnounce->isSubmitted() && $searchAnnounce->isValid()) {
            // si le formulaire est envoyé et validé alors :
            $request->query->remove('_token');
            // on passe le formulaire a la fonction du repository qui est un tableau classic : AnnounceRepository.php

            return $this->redirectToRoute('app_result', $request->query->all());
        };

        return $this->render('home/index.html.twig', [
            'searchAnnounce' => $searchAnnounce->createView(),
            'announces'=>$announces,
        ]);
    }
}
