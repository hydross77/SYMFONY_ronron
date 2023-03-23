<?php

namespace App\DataFixtures;

use App\Entity\Announce;
use App\Repository\CatRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;
use App\Repository\UserRepository;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;


class AnnounceFixtures extends Fixture implements DependentFixtureInterface
{
    protected UserRepository $userRepository;
    protected CatRepository $catRepository;

    public function __construct(UserRepository $userRepository, CatRepository $catRepository)
    {
        $this->userRepository = $userRepository;
        $this->catRepository = $catRepository;
    }


    public function load(ObjectManager $manager)
    {
        $faker = Faker::create('fr_FR');

        $users = $this->userRepository->findAll();
        $cats = $this->catRepository->findAll();

        $usersLength = count($users)-1;
        $catsLength = count($cats)-1;

        for ($i=0; $i < 100; $i++) {
            // permet d'avoir un utilisateur random
            // possible à faire avec Faker mais plus lourd en ressource
            $randomKey = rand(0, $usersLength);
            $randomKeyCat = rand(0, $catsLength);
            $user = $users[$randomKey];
            $cat = $cats[$randomKeyCat];

            $annonce = new Announce();
            $annonce
                ->setType($faker->randomElement(['lost', 'found']))
                ->setDateCat($faker->dateTime())
                ->setDescription($faker->sentences(3, true))
                ->setCp($faker->randomElement(['67200', '67000']))
                ->setStreet($faker->randomElement(['Chemin du grossroethig', 'Route des romains']))
                ->setCity($faker->randomElement(['Strasbourg', 'Lyon', 'Marseille', 'Bordeaux', 'Paris', 'Nantes', 'Nice']))
                ->setCountry('France')
                ->setUser($user)
                ->setCat($cat)
            ;
            $manager->persist($annonce);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return array(
            UserFixture::class,
            CatFixture::class
        );
    }

}
