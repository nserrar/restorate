<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Media;
use App\Entity\Restaurant;
use App\Entity\Review;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $medias = [];
        $cities = [];
        $users = [];
        for ($i = 0; $i < 10; $i++) {
            //generate cities
            $city = new City();
            $city->setName($faker->city);
            $manager->persist($city);
            $cities[] = $city;

            //generate users
            $user = new User();
            $user->setUsername($faker->userName)
                ->setLastName($faker->lastName)
                ->setFirstName($faker->firstName)
                ->setPassword('$2y$13$igyKf3g2EaZNJPIuM9vZ1uLv26RDgng7qQUGbyLjzz01hEI.pbfEm');//pass = 12345
            if($i % 2 == 0){
                $user->setRoles(['ROLE_CLIENT']);
            }else{
                $user->setRoles(['ROLE_RESTAURATEUR']);
            }
            $manager->persist($user);
            $users[] = $user;
        }
        $manager->flush();

        //generate restaurants
        $restaurants[] = (new Restaurant())
        ->setOwner($users[0])
            ->setCity($cities[0])
            ->setPostalCode(rand(10000, 30000))
            ->setName('Nikamo');

        $restaurants[] = (new Restaurant())
        ->setOwner($users[1])
            ->setCity($cities[1])
            ->setPostalCode(rand(10000, 30000))
            ->setName('Le Gouthé');

        $restaurants[] = (new Restaurant())
        ->setOwner($users[2])
            ->setCity($cities[2])
            ->setPostalCode(rand(10000, 30000))
            ->setName('MacDonalds');

        $restaurants[] = (new Restaurant())
        ->setOwner($users[3])
            ->setCity($cities[3])
            ->setPostalCode(rand(10000, 30000))
            ->setName('Tijiay');

        $restaurants[] = (new Restaurant())
            ->setOwner($users[4])
            ->setCity($cities[4])
            ->setPostalCode(rand(10000, 30000))
            ->setName('Tarino');

        $restaurants[] = (new Restaurant())
            ->setOwner($users[5])
            ->setCity($cities[5])
            ->setPostalCode(rand(10000, 30000))
            ->setName('Malawi');

        $restaurants[] = (new Restaurant())
            ->setOwner($users[6])
            ->setCity($cities[6])
            ->setPostalCode(rand(10000, 30000))
            ->setName('Castaliano');

        $restaurants[] = (new Restaurant())
            ->setOwner($users[7])
            ->setCity($cities[7])
            ->setPostalCode(rand(10000, 30000))
            ->setName('Taritio');

        $restaurants[] = (new Restaurant())
            ->setOwner($users[8])
            ->setCity($cities[8])
            ->setPostalCode(rand(10000, 30000))
            ->setName('The perl Haribo');

        $restaurants[] = (new Restaurant())
            ->setOwner($users[9])
            ->setCity($cities[9])
            ->setPostalCode(rand(10000, 30000))
            ->setName('Taetisa');




        for ($i = 0; $i < 10; $i++) {
            //generate medias
            $media = new Media();
            $media->setFile('public/media/'.$i.'png')
                ->setRestaurant($restaurants[$i]);
            $manager->persist($media);
            $manager->persist($restaurants[$i]);
        }

        $manager->flush();

        foreach ($restaurants as $restaurant){
            for($i = 0; $i < 10; $i++){
                //add reviews
                $review = new Review();
                $review->setRestaurant($restaurant)
                    ->setUser($users[9-$i])
                    ->setComment('Awesome Restaurant !')
                    ->setRating(rand(0, 5));

                $restaurant->addReview($review);
                $manager->persist($review);
                $manager->persist($restaurant);
            }

            $manager->flush();
        }
    }
}