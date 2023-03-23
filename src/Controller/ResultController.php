<?php

namespace App\Controller;

use App\Form\SearchForm2;
use App\Repository\AnnounceRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ResultController extends AbstractController
{
    #[Route('/search/announces', name: 'app_result')]
    public function index(Request $request, AnnounceRepository $repository, PaginatorInterface $paginator): Response
    {
        $query = $request->query->get('type');
        $city = $request->query->get('city');
        $color = $request->query->get('color');

        $user = $this->getUser() ?? null;
        /*$freelances = $repository->findSearch([
            'q' => $query,
            'city' => $city
        ]);*/
        // les profils paginé
        $announces = $paginator->paginate(
            $repository->findSearch2([
                'type' => $query,
                'city' => $city,
                'color' => $color,
            ]),
            $request->query->getInt('page', 1),
            12
        );

        $searchAnnounce = $this->createForm(SearchForm2::class, null);
        // crée le formulaire configuré dans le dossier FORM
        $searchAnnounce->handleRequest($request);
        // traite les données du formulaire


        if ($searchAnnounce->isSubmitted() && $searchAnnounce->isValid()) {
            // si le formulaire est envoyé et validé alors :
            $request->query->remove('_token');
            // on passe le formulaire a la fonction du repository qui est un tableau classic : FreelanceRepository.php

            return $this->redirectToRoute('app_result', $request->query->all());
        };

        return $this->render('result/index.html.twig', [
            'announces' => $announces ?? null,
            'user' => $user ?? null,
            'searchAnnounce2' => $searchAnnounce->createView()
        ]);
    }
}
