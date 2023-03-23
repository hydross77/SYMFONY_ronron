<?php

namespace App\Controller;

use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function contact(MailerInterface $mailer,Request $request): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mail = (new Email())
                ->from($form->get('email')->getData())
                ->to(new Address('contactez.ronron@gmail.com', 'Ronron'))
                ->subject($form->get('objet')->getData())
                ->text($form->get('message')->getData())
            ;
            $mailer->send($mail);
            $this->addFlash('message', 'Votre demande à bien été prise en compte');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);

    }
}
