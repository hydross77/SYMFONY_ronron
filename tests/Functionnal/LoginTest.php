<?php

namespace App\Tests\Functionnal;


use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

//Commande terminal : php bin/phpunit --filter LoginTest


class LoginTest extends WebTestCase
{

    public function testIfLoginIsSuccessful(): void
    {
        // Création d'un client qui permettra de faire des requêtes HTTP
        $client = static::createClient();

        // Récupération du générateur d'URL
        /** @var UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $client->getContainer()->get("router");

        // Requête GET sur la page de connexion
        $crawler = $client->request('GET', $urlGenerator->generate('app_login'));

        // Récupération du formulaire et remplissage des champs
        $form = $crawler->filter("form[name=login]")->form([
            "email" => "admin@ronron.fr",
            "password" => "admin"
        ]);

        // Soumission du formulaire
        $client->submit($form);

        // Vérification que la réponse est une redirection (code 302)
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        // Suivi de la redirection
        $client->followRedirect();

        // Vérification que la route actuelle est "app_home"
        $this->assertRouteSame('app_home');
    }

}