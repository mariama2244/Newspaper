<?php

namespace App\DataFixtures;

use DateTime;
use App\Entity\Category;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }


    public function load(ObjectManager $manager): void
    {
       $categories = [
        'Cinéma',
        'Sport',
        'Musique',
        'Science',
        'Ecologie',
        'Mode',
        'Société',
        'Hi-Tech',
        'Politique',
        'Jeaux vidéo'

       ];

       foreach($categories as $name) {

        $category = new Category();

        $category->setName($name);
        $category->setAlias($this->slugger->slug($name)); # you need to create the private function

        $category->setCreatedAt(new DateTime());
        $category->setUpdatedAt(new DateTime());

        $manager->persist($category);
       }
        $manager->flush();
    }
}
