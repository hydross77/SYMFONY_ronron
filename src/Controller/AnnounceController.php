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
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\Workflow\WorkflowInterface;

class AnnounceController extends AbstractController
{
    private $entityManager;
    private $announceWorkflow;

    public function __construct(EntityManagerInterface $entityManager, WorkflowInterface $announceWorkflow)
    {
        $this->entityManager = $entityManager;
        $this->announceWorkflow = $announceWorkflow;
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
            $cat->addUser($user);

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

            // Associate the current user with the announcement
            $announce->setUser($user);

            $this->entityManager->persist($user);

            try{
                $this->announceWorkflow->apply($announce, 'to_online');
            }catch(\LogicException $exception){

            }

            $this->entityManager->persist($announce);
            $this->entityManager->flush();

            // Redirect to the page of the created announcement
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
        $color = $cat->getColor();

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
            'color'=>$color,
            'comment_form' => $form,
            'comments' => $comments,
        ]);
    }

    #[Route('/announce/{id}/change_state/{state}', name: 'app_announce_change_state')]
    public function changeState(Announce $announce, string $state): Response
    {
        // Vérifier que l'utilisateur connecté est le propriétaire de l'annonce
        $user = $this->getUser();
        if (!$announce->getUser() || $announce->getUser()->getId() !== $user->getId()) {
            throw new AccessDeniedHttpException("Vous n'êtes pas autorisé à changer l'état de cette annonce.");
        }

        // Vérifier que l'état demandé est une transition valide
        if (!$this->announceWorkflow->can($announce, $state)) {
            throw new BadRequestHttpException("La transition $state n'est pas autorisée pour cette annonce.");
        }

        // Appliquer la transition demandée sur l'annonce
        try {
            $this->announceWorkflow->apply($announce, $state);
        } catch (LogicException $e) {
            throw new BadRequestHttpException("Impossible de changer l'état de l'annonce : " . $e->getMessage());
        }

        // Enregistrer les modifications dans la base de données
        $this->entityManager->flush();

        // Rediriger vers la page de l'annonce modifiée
        return $this->redirectToRoute('app_announce_show', ['id' => $announce->getId()]);
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
