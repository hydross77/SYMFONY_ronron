<?php

namespace App\DataFixtures;

use App\Entity\Cat;
use App\Repository\CatRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;

class CatFixture extends Fixture
{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create('fr_FR');

        $users = $this->userRepository->findAll();
        $usersLength = count($users)-1;


        for ($i=0; $i < 100; $i++) {
            $randomKey = rand(0, $usersLength);
            $user = $users[$randomKey];

            $cat = new Cat();
            $cat
                ->setName($faker->randomElement(['Pichu', 'Dwitch', 'Kacha']))
                ->setAge($faker->randomElement(['2', '3', '8']))
                ->setBreed($faker->randomElement(['Aucune', 'Maincoon', 'Egyptien']))
                ->setTattoo($faker->randomElement(['Oui', 'Non']))
                ->setSterelized($faker->randomElement(['Oui', 'Non']))
                ->setDesignCoat($faker->randomElement(['Tacheté', 'Uni', 'Tigré']))
                ->setLengthCoat($faker->randomElement(['Court', 'Long']))
                ->setSexe($faker->randomElement(['Mâle', 'Femelle']))
                ->setPicture($faker->randomElement(['chat1.jpg', 'chat2.jpg', 'chat3.jpg', 'chat4.jpeg', 'chat5.jpg', 'chat6.jpg']))
                ->setChip($faker->randomElement(['Oui', 'Non']))
                ->setUser($user)
            ;
            $manager->persist($cat);
        }

        $manager->flush();
    }
}
