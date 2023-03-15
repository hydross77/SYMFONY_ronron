<?php

namespace App\Controller\Admin;

use App\Entity\Announce;
use App\Entity\Cat;
use App\Entity\Comment;
use App\Entity\Donation;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {


        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());


    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Ronron');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');
        yield MenuItem::linkToCrud('Utilisateur', 'fa-solid fa-user', User::class);
        yield MenuItem::linkToCrud('Annonce', 'fa-solid fa-list', Announce::class);
        yield MenuItem::linkToCrud('Chat', 'fa-solid fa-cat', Cat::class);
        yield MenuItem::linkToCrud('Donation', 'fa-sharp fa-solid fa-money-bill', Donation::class);
        yield MenuItem::linkToCrud('Commentaire', 'fa-solid fa-comment', Comment::class);

    }
}
