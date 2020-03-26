<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{

    private $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager)
    {
        $faker = \Faker\Factory::create('fr_FR');
        $users = [];
        $categories = [];

        for ($i = 1; $i <= 10; $i++) {
            $user = new User();
            $category = new Category();
            $user->setUsername($faker->email);
            $category->setName(ucfirst($faker->word));
            $category->setDescription(random_int(1,5) > 1 ? $faker->sentence(5,true) : null);
            $category->setSlug($this->slugger->slug($category->getName())->lower());

            $manager->persist($user);
            $manager->persist($category);
            $users[$i] = $user;
            $categories[$i] = $category;
        }

        for($i = 0;$i < 100;$i++){
        $product = new Product();
        $product->setName("IPhone ".$i);
        $product->setDescription($faker->sentence(12,true));
        $product->setPrice($faker->numberBetween(10,1000));
        $product->setSlug("iphone-".$i);
        $product->setUser($faker->randomElement($users));
        $product->setCategory($faker->randomElement($categories));

        $manager->persist($product);
        }

        $manager->flush();
    }
}
