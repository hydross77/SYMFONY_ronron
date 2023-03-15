<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * génère de fausses données pour l'entité User
 */
class UserFixture extends Fixture
{
    public const AUTHOR_USER_REFERENCE = 'author-user';

    private $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {

        $faker = Faker::create('fr_FR');


        $admin = new User();
        $admin
            ->setEmail('admin@ronron.fr')
            ->setPseudo('Ronron')
            ->setRoles(['ROLE_ADMIN'])
            ->setPassword($this->encoder->hashPassword($admin, 'admin'))
        ;
        $manager->persist($admin);

        $password = $this->encoder->hashPassword(new user(), 'password');

        for ($i=0; $i < 5; $i++) {
            $user = new User();
            $user
                ->setEmail($faker->email())
                ->setPassword($password)
                ->setPseudo($faker->firstName)
            ;
            $manager->persist($user);
        }

        $manager->flush();

        $this->addReference(self::AUTHOR_USER_REFERENCE, $user);
    }
}