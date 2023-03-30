<?php

namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class DonationController extends AbstractController
{

    #[Route('/donation', name: 'app_donation_stripe')]
    public function index(Request $request): RedirectResponse
    {
        $url = 'https://buy.stripe.com/test_14kg2abmEdAZem4dQQ';
        return new RedirectResponse($url);
    }

}
