<?php

namespace App\DataFixtures;

use App\Entity\Products;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductsFixtures extends Fixture implements DependentFixtureInterface
{

    public function __construct(private SluggerInterface $slugger){}

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        for($prod = 1; $prod <= 10; $prod++){
            $product = new Products();
            $product->setName($faker->text(5));
            $product->setDescription($faker->text());
            $product->setSlug($this->slugger->slug($product->getname())->lower());
            $product->setPrice($faker->numberBetween(900,150000));
            $product->setStock($faker->numberBetween(0,10));

            // On recupère la catégorie pour les produits
            $category = $this->getReference('cat-'. rand(1,7));
            $product->setCategories($category);
            $this->setReference('prod-'. $prod, $product);
            
            $manager->persist($product);
            
        }        
        
        $manager->flush();
    }

    // Execution dans l'odre des dépendances(CatégorieFixtures avant ProductsFixtures)
    public function getDependencies():array
    {
        return [
            CategoriesFixtures::class
        ];
    }
}
