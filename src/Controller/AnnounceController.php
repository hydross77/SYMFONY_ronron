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
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\Workflow\Exception\LogicException;
use Symfony\Component\Workflow\WorkflowInterface;
use Facebook\Facebook;

class AnnounceController extends AbstractController
{
    private $entityManager;
    private $announceWorkflow;

    public function __construct(EntityManagerInterface $entityManager, WorkflowInterface $announceWorkflow)
    {
        $this->entityManager = $entityManager;
        $this->announceWorkflow = $announceWorkflow;
    }

    #[Route('/myannounce/{id}', name: 'app_announce')]
    public function index(int $id): Response
    {
        // Récupére l'utilisateur connecté
        $user = $this->getUser();

        // Vérifie si l'utilisateur est connecté
        if ($user === null) {
            // Rediriger vers une page d'erreur ou retourner une réponse appropriée
            // en cas d'utilisateur non connecté
            return $this->render('utilisation/error.html.twig');
        }

        // Récupère les annonces de l'utilisateur
        $announces = $user->getAnnounces();

        // Passe les annonces à la vue pour les afficher
        return $this->render('announce/index.html.twig', [
            'announces' => $announces,
        ]);
    }

    #[Route('/deleteannounce/{id}', name: 'app_delete_announce')]
    public function deleteAnnounce(Request $request, EntityManagerInterface $entityManager, $id): Response
    {

        $user = $this->getUser();

        // Récupère l'annonce à partir de l'identifiant
        $announce = $entityManager->getRepository(Announce::class)->find($id);

        // Vérifie si l'annonce existe
        if (!$announce) {
            throw $this->createNotFoundException('Annonce non trouvée');
        }

        // Vérifie si l'utilisateur est propriétaire de l'annonce
        if ($this->getUser() !== $announce->getUser()) {
            throw $this->createAccessDeniedException('Vous n\'êtes pas autorisé à supprimer cette annonce.');
        }

        // Supprime l'annonce
        $entityManager->remove($announce);
        $entityManager->flush();

        // Redirige vers la page "Mes annonces" avec un message de succès
        $this->addFlash('success', 'L\'annonce a été supprimée avec succès.');
        return $this->redirectToRoute('app_announce', ['id' => $user->getId()]);
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
    public function newCat(Request $request, SessionInterface $session, UserPasswordHasherInterface $userPasswordHasher, SluggerInterface $slugger): Response
    {
        $cat = new Cat();

        $formCat = $this->createForm(CatType::class, $cat);
        $formCat->handleRequest($request);

        if ($formCat->isSubmitted() && $formCat->isValid()) {
            $colors = $formCat->get('colors')->getData();
            foreach ($colors as $color) {
                if (!$color instanceof Color) {
                    $colorId = $color->getId();
                    $colorRepository = $this->entityManager->getRepository(Color::class);
                    $color = $colorRepository->find($colorId);
                }
                $cat->addColor($color);
            }


            $pictureProfil = $formCat->get('picture')->getData();

            if ($pictureProfil) {
                $originalFilename = pathinfo($pictureProfil->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $pictureProfil->guessExtension();

                try {
                    $pictureProfil->move(
                        $this->getParameter('picture'),
                        $newFilename
                    );
                } catch (FileException $error) {
                }
                $cat->setPicture($newFilename);
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
    #[Route("/announce/update/{id}", name: 'update_announce')]
    public function create(Request $request, SessionInterface $session, Announce $announce = null): Response
    {
        $catId = $session->get('cat_id');
        $user = $this->getUser();

        if (!$announce){
        $announce = new Announce();
        }

        $announce->setUser($user);
        $announce->setCat($this->entityManager->getRepository(Cat::class)->find($catId));

        $form = $this->createForm(AnnounceType::class, $announce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $announce = $form->getData();
            $user = $this->getUser();
            $announce->setUser($user);
            $announce->setCountry('France');
            $this->entityManager->persist($user);

            try {
                $this->announceWorkflow->apply($announce, 'to_online');
            } catch (\LogicException $exception) {
            }

            $this->entityManager->persist($announce);
            $this->entityManager->flush();
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

        $user = $this->getUser() ?? null;

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

            // Envoie une notification par email à l'utilisateur de l'annonce
            $announceUser = $announce->getUser();
            if ($announceUser) {
                $email = (new Email())
                    ->from('contactez.ronron@gmail.com')
                    ->to($announceUser->getEmail())
                    ->subject('Nouveau commentaire sur votre annonce de chat perdu')
                    ->html('<p>Bonjour,</p><p>Un utilisateur a posté un nouveau commentaire sur votre annonce de chat perdu : "'.$announce->getTitle().'".</p><p>Consultez votre annonce pour lire le commentaire.</p><p>Merci de votre utilisation de notre site.</p>');

                $mailer->send($email);
            }


            // Envoie un email de signalement à l'administrateur
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

            // Envoie la notification à l'administrateur
            $reason = $reportAnnounce->get('reason')->getData();
            $details = $reportAnnounce->get('details')->getData();

            $announcement = $this->entityManager->getRepository(Announce::class)->find($id);

            if (!$announcement) {
                throw $this->createNotFoundException('Annonce non trouvée');
            }
            $user = $this->getUser();
            // Envoie un email de signalement à l'administrateur
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
            'user'=>$user,
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
        // Vérifie que l'utilisateur connecté est le propriétaire de l'annonce
        $user = $this->getUser();
        if (!$announce->getUser() || $announce->getUser()->getId() !== $user->getId()) {
            throw new AccessDeniedHttpException("Vous n'êtes pas autorisé à changer l'état de cette annonce.");
        }

        // Vérifie que l'état demandé est une transition valide
        if (!$this->announceWorkflow->can($announce, $state)) {
            throw new BadRequestHttpException("La transition $state n'est pas autorisée pour cette annonce.");
        }

        // Applique la transition demandée sur l'annonce
        try {
            $this->announceWorkflow->apply($announce, $state);
        } catch (LogicException $e) {
            throw new BadRequestHttpException("Impossible de changer l'état de l'annonce : " . $e->getMessage());
        }

        // Enregistre les modifications dans la base de données
        $this->entityManager->flush();

        // Redirige vers la page de l'annonce modifiée
        return $this->redirectToRoute('app_announce_show', ['id' => $announce->getId()]);
    }


    #[Route('/delete/{id}', name: 'app_comment_delete')] // Définit l'URL pour cette fonction
    public function delete(Request $request, Comment $comment, Security $security): Response
    {
        // Vérifie que l'utilisateur est connecté et qu'il a les autorisations nécessaires pour supprimer le commentaire
        if (!$security->isGranted('ROLE_USER')) {
            throw new AccessDeniedException('Vous devez être connecté pour supprimer un commentaire.');
        }

        // Vérifie si l'utilisateur connecté est le propriétaire du commentaire
        if ($comment->getUser() !== $security->getUser()) {
            throw new AccessDeniedException('Vous n\'êtes pas autorisé à supprimer ce commentaire.');
        }

        // Supprime le commentaire en utilisant Doctrine EntityManager
        $this->entityManager->remove($comment);
        $this->entityManager->flush();

        // Ajoute un message flash pour informer l'utilisateur que le commentaire a été supprimé avec succès
        $this->addFlash('message', 'Votre commentaire a bien été supprimé');

        // Redirige l'utilisateur
        return $this->redirect($request->headers->get('referer'));
    }



    /**
     * @Route("/announce/{id}/share/facebook", name="announce_share_facebook")
     */
    public function shareOnFacebook(EntityManagerInterface $entityManager, Request $request, $id): \Symfony\Component\HttpFoundation\RedirectResponse
    {
        // Récupére l'annonce à partager
        $announce = $entityManager->getRepository(Announce::class)->find($id);

        // Vérifie si l'annonce existe
        if (!$announce) {
            throw $this->createNotFoundException('L\'annonce n\'existe pas.');
        }

        // Configure le SDK Facebook
        $fb = new Facebook([
            'app_id' => $_ENV['FACEBOOK_APP_ID'],
            'app_secret' => $_ENV['FACEBOOK_APP_SECRET'],
            'default_graph_version' => 'v11.0',
        ]);

        // Crée l'URL de partage sur Facebook
        $shareUrl = $request->getSchemeAndHttpHost() . $this->generateUrl('app_announce_show', ['id' => $id], UrlGeneratorInterface::ABSOLUTE_URL);
        $shareParams = [
            'link' => $shareUrl,
        ];
        $loginUrl = $fb->getRedirectLoginHelper()->getLoginUrl($shareUrl, ['publish_actions'], http_build_query($shareParams, '', '&', PHP_QUERY_RFC3986));

        // Redirige vers l'URL de partage sur Facebook
        return $this->redirect($loginUrl);
    }

}
