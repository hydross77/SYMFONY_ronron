<?php

namespace App\Controller;

use App\Entity\Announce;
use App\Entity\Cat;
use App\Entity\Color;
use App\Entity\Comment;
use App\Entity\User;
use App\Form\AnnounceType;
use App\Form\CatType;
use App\Form\CommentType;
use App\Form\RegistrationFormType;
use App\Form\ReportAnnounceType;
use App\Form\ReportType;
use App\Repository\CommentRepository;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
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

    #[Route('/new', name: 'app_new')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppAuthenticator $authenticator,): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );

            return $this->redirectToRoute('app_new_cat');
        }

        return $this->render('announce/new_account.html.twig', [
            'formRegister' => $form->createView(),
        ]);
    }


    #[Route('/new/cat', name: 'app_new_cat')]
    public function newCat(Request $request, SessionInterface $session, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $cat = new Cat();

        $formCat = $this->createForm(CatType::class, $cat);
        $formCat->handleRequest($request);

        if ($formCat->isSubmitted() && $formCat->isValid()) {
            $color = $formCat->get('color')->getData();
            if ($color instanceof Color) {
                $cat->addColor($color);
            } else {
                $colorId = $color;
                $colorRepository = $this->entityManager->getRepository(Color::class);
                $color = $colorRepository->find($colorId);
                $cat->addColor($color);
            }

            $userId = $this->getUser()->getId();
            $userRepository = $this->entityManager->getRepository(User::class);
            $user = $userRepository->find($userId);
            $cat->setUser($user);

            $this->entityManager->persist($cat);
            $this->entityManager->flush();

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

            try {
                $this->announceWorkflow->apply($announce, 'to_online');
            } catch (\LogicException $exception) {

            }

            $this->entityManager->persist($announce);
            $this->entityManager->flush();

            // Redirection vers l'annonce crée
            $announceId = $announce->getId();

            return $this->redirectToRoute('app_announce_show', ['id' => $announceId]);
        }
        return $this->render('announce/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/announce/{id}', name: 'app_announce_show')]
    public function show($id, Request $request, CommentRepository $commentRepository, MailerInterface $mailer): Response
    {
        $announce = $this->entityManager->getRepository(Announce::class)->find($id);

        if (!$announce) {
            throw new NotFoundHttpException("L'annonce avec l'ID $id n'existe pas.");
        }
        $comments = $announce->getComments(['createdAt' => 'DESC']);

        $cat = $announce->getCat();
        $color = $cat->getColor();

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $report = $this->createForm(ReportType::class);
        $reportAnnounce = $this->createForm(ReportAnnounceType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAnnounce($announce);

            $user = $this->getUser();
            $comment->setUser($user);

            $this->entityManager->persist($comment);
            $this->entityManager->flush();

            return $this->redirectToRoute('app_announce_show', ['id' => $announce->getId()]);
        }

        $report->handleRequest($request);

        if ($report->isSubmitted() && $report->isValid()) {
            $data = $report->getData();
            $comment = $this->entityManager->getRepository(Comment::class)->find($id);
            $commentId = $comment->getId();
            $reason = $data['reason'];
            $content = $data['content'];

            $user = $this->getUser();


            // Envoyer un email de signalement à l'administrateur
            $email = (new Email())
                ->from($user->getEmail())
                ->to('contactez.ronron@gmail.com')
                ->subject('Signalement de commentaire')
                ->html(sprintf('Annonce ID: %d<br>Commentaire ID: %d<br>Raison: %s<br>Contenu: %s', $announce->getId(), $commentId, $reason, $content));
            $mailer->send($email);

            $this->addFlash('message', 'Le commentaire a été signalé avec succès. Nous le vérifierons dans les plus brefs délais.');

            return $this->redirectToRoute('app_announce_show', ['id' => $announce->getId()]);
        }

        $reportAnnounce->handleRequest($request);

        if ($reportAnnounce->isSubmitted() && $reportAnnounce->isValid()) {

            // Envoyer la notification à l'administrateur
            $reason = $reportAnnounce->get('reason')->getData();
            $details = $reportAnnounce->get('details')->getData();

            $announcement = $this->entityManager->getRepository(Announce::class)->find($id);

            if (!$announcement) {
                throw $this->createNotFoundException('Annonce non trouvée');
            }
            $user = $this->getUser();
            // Envoyer un email de signalement à l'administrateur
            $email = (new Email())
                ->from($user->getEmail())
                ->to('contactez.ronron@gmail.com')
                ->subject('Signalement d\'une annonce')
                ->html(sprintf('Annonce ID: %d<br><br><br>Raison: %s<br>Contenu: %s', $announcement->getId(), $reason, $details));

            $mailer->send($email);

            $this->addFlash('message', 'Annonce signalé');

            return $this->redirectToRoute('app_announce_show', ['id' => $announce->getId()]);
        }

        return $this->render('announce/show.html.twig', [
            'announce' => $announce,
            'cat' => $cat,
            'color' => $color,
            'comment_form' => $form->createView(),
            'report_form' => $report->createView(),
            'report_announce' => $reportAnnounce->createView(),
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
