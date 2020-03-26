<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');
        for($i = 0;$i < 100;$i++){
        $product = new Product();
        $product->setName("IPhone ".$i);
        $product->setDescription($faker->sentence(12,true));
        $product->setPrice($faker->numberBetween(10,1000));
        $product->setSlug("iphone-".$i);
        $manager->persist($product);
        }

        $manager->flush();
    }
}
