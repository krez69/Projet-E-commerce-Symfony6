<?php

namespace App\DataFixtures;

use App\Entity\Categories;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;


class CategoriesFixtures extends Fixture
{
    private $counter = 1;

    public function __construct(private SluggerInterface $slugger){}

    
    public function load(ObjectManager $manager): void
    {
        $parent = $this->createCategory('Informatique', null, $manager);    
        $this->createCategory('Ordinateurs portables', $parent, $manager);
        $this->createCategory('PC gameur', $parent, $manager);

        $parent = $this->createCategory('Jeux', null, $manager);
        $this->createCategory('Ps4', $parent, $manager);
        $this->createCategory('Xbox', $parent, $manager); 
        $this->createCategory('Pc', $parent, $manager); 


        $manager->flush();
    }

    public function createCategory(string $name, Categories $parent = null , ObjectManager $manager)
    {
        $category = new Categories();
        $category->setName($name);
        $category->setSlug($this->slugger->slug($category->getname())->lower());
        $category->setParent($parent);
        $manager->persist($category);

        // créer une réference pour chaque catégorie
        $this->addReference('cat-'.$this->counter, $category);
        $this->counter++;

        
        return $category;

    }
}
