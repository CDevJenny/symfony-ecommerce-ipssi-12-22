<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Brand;
use App\Entity\Product;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(protected ManagerRegistry $registry, protected UserPasswordHasherInterface $hasher)
    {
    }
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setFirstname('Gilbert');
        $user->setLastname('Redbull');
        $user->setEmail('gilbert@test.com');
        $user->setPassword($this->hasher->hashPassword($user, 'mdp1234'));
        $user->setRoles(["ROLE_USER"]);
        $ur = $this->registry->getRepository(User::class);
        $ur->save($user, true);

        $category = new Category();
        $category->setName('Energy Drink');
        $cr = $this->registry->getRepository(Category::class);
        $cr->save($category, true);

        $brand = new Brand();
        $brand->setName('Redbull');
        $br = $this->registry->getRepository(Brand::class);
        $br->save($brand, true);


        for ($i = 0; $i < 20; $i++) {
            $product = new Product();
            $product->setName('product ' . $i);
            $product->setExcerpt('Sit aliqua esse est Lorem pariatur enim ex labore.');
            $product->setDescription('Veniam officia voluptate eu sint eu et. Minim nulla adipisicing veniam in consequat. Aliquip ad sunt minim tempor.Ullamco ex dolor consectetur nisi consectetur non minim excepteur sint. Reprehenderit tempor irure cillum eiusmod irure in. Amet irure ad magna sunt minim amet non non deserunt mollit ex anim ex commodo. Cillum qui elit eiusmod sunt reprehenderit cillum aliquip. Eiusmod et voluptate aliqua incididunt eu labore. Mollit cillum Lorem nisi exercitation commodo qui amet nulla laborum commodo do officia et.');
            $product->setImage('https://media.auchan.fr/A0220080425000581136PRIMARY_1200x1200/B2CD/');
            $product->setQuantity(mt_rand(1, 15));
            $product->setSold(mt_rand(1, 5));
            $product->setPrice(mt_rand(10, 100));
            $product->setStatus(mt_rand(1, 2));
            $product->setSeller($user);
            $product->setBrand($brand);
            $product->setCategory($category);
            $manager->persist($product);
        }
        $manager->flush();
    }
}
