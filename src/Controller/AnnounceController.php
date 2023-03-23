<?php

namespace App\Controller;

use App\Entity\Announce;
use App\Entity\Cat;
use App\Entity\Comment;
use App\Form\AnnounceType;
use App\Form\CatType;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AnnounceController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/new/cat', name: 'app_new_cat')]
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

        return $this->render('announce/new_cat.html.twig', [
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

            // Rediriger vers la page de l'annonce créée
            $announceId = $announce->getId();
            return $this->redirectToRoute('app_announce_show', ['id' => $announceId]);
        }

        return $this->render('announce/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/announce/{id}', name: 'app_announce_show')]
    public function show($id, Request $request, CommentRepository $commentRepository): Response
    {
        $announce = $this->entityManager->getRepository(Announce::class)->find($id);

        if (!$announce) {
            throw new NotFoundHttpException("L'annonce avec l'ID $id n'existe pas.");
        }

        $cat = $announce->getCat();

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAnnounce($announce);

            $user = $this->getUser();
            $comment->setUser($user);

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_announce_show', ['id' => $announce->getId()]);
        }

        $comments = $commentRepository->findBy(['announce' => $announce], ['createdAt' => 'DESC']);

        return $this->render('announce/show.html.twig', [
            'announce' => $announce,
            'cat' => $cat,
            'comment_form' => $form,
            'comments' => $comments,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_comment_delete')]
    public function delete(Request $request, Comment $comment): Response
    {
        $this->entityManager->remove($comment);
        $this->entityManager->flush();
        $this->addFlash('message', 'Votre commentaire a bien été supprimé');
        return $this->redirect($request->headers->get('referer'));
    }
}
