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
        $query = $request->request->get('type');
        $city = $request->request->get('city');
        $breed = $request->request->get('breed');
        $length = $request->request->get('length_coat');
        $design = $request->request->get('design_coat');
        $sexe = $request->request->get('sexe');

        $searchAnnounce = $this->createForm(SearchForm2::class, null);
        $searchAnnounce->handleRequest($request);

        if ($searchAnnounce->isSubmitted() && $searchAnnounce->isValid()) {
            $parameters = $request->query->all();
            $color = $searchAnnounce->get('color')->getData();
            $parameters['color'] = is_array($color) ? array_filter($color, 'is_scalar') : [$color];


            $announces = $paginator->paginate(
                $repository->findSearch($parameters),
                $request->query->getInt('page', 1),
                12,
            );

        } else {
            $announces = $paginator->paginate(
                $repository->findSearch([
                    'type' => $query,
                    'city' => $city,
                    'breed' => $breed,
                    'sexe' => $sexe,
                    'length_coat' => $length,
                    'design_coat' => $design,
                ]),
                $request->query->getInt('page', 1),
                12,
            );
        }


        return $this->render('result/index.html.twig', [
            'announces' => $announces ?? null,
            'user' => $user ?? null,
            'searchAnnounce2' => $searchAnnounce->createView()
        ]);
    }

}
