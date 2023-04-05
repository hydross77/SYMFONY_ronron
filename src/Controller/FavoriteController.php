<?php

namespace App\Controller;

use App\Entity\Cat;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Knp\Component\Pager\PaginatorInterface;

class FavoriteController extends AbstractController
{
    #[Route('/favorite', name: 'app_favorite')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $user = $this->getUser();
        $favorites = $paginator->paginate(
            $user->getFavorite(),
            $request->query->getInt('page', 1),
            12
        );

        return $this->render('favorite/index.html.twig', [
            'controller_name' => 'FavoriteController',
            'favorites' => $favorites,
            'user' => $user,
        ]);
    }

    #[Route('/favorite/{id}', name: 'app_favorite_toggle', methods: ['POST'])]
    public function addFavorite(EntityManagerInterface $entityManager, $id): Response
    {
        $cat = $entityManager->getRepository(Cat::class)->find($id);

        if (!$cat) {
            throw $this->createNotFoundException('Le chat avec l\'ID ' . $id . ' n\'existe pas.');
        }

        $user = $this->getUser();
        $user->addFavorite($cat);
        $entityManager->flush();

        $this->addFlash('success', 'Le chat a été ajouté en Favoris.');

        return $this->json([
            'success' => true,
        ], Response::HTTP_ACCEPTED);
    }

    #[Route('/favorite/{id}', name: 'app_favorite_delete', methods: ['DELETE'])]
    public function deleteFavorite(EntityManagerInterface $entityManager, $id): Response
    {
        $cat = $entityManager->getRepository(Cat::class)->find($id);

        if (!$cat) {
            throw $this->createNotFoundException('Le chat avec l\'ID ' . $id . ' n\'existe pas.');
        }

        $user = $this->getUser();
        $user->removeFavorite($cat);
        $entityManager->flush();

        $this->addFlash('success', 'Le chat a été supprimé des Favoris.');

        return $this->json([
            'success' => true,
        ], Response::HTTP_ACCEPTED);
    }
}
