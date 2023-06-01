<?php

namespace App\Tests\Entity;

use App\Entity\Color;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

// Commande terminal : php bin/phpunit --filter ColorTest

// Importe la classe KernelTestCase du composant Symfony pour faciliter les tests d'intégration
class ColorTest extends KernelTestCase
{
    // Teste la validation de l'entité Framework
    /**
     * @throws \Exception
     */
    public function testValidEntity()
    {
        // Lance le noyau de l'application Symfony
        self::bootKernel();

        // Récupère le conteneur de service de l'application
        $container = static::getContainer();

        // Crée une nouvelle instance de l'entité Framework
        $color = new Color();

        // Définit le nom du framework.
        $color->setName('Gris');

        // Valide l'entité avec le service Validator et récupère les erreurs éventuelles
        $errors = $container->get('validator')->validate($color);

        // Vérifie que le nombre d'erreurs est égal à 0
        $this->assertCount(0, $errors);
    }
}