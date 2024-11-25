<?php

namespace App\DataFixtures;

use App\Entity\Marque;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $marques = ['BMW', 'Peugeot', 'Mercedes', 'Renault', 'Toyota'];

        foreach ($marques as $nomMarque) {
            $marque = new Marque();
            $marque->setNom($nomMarque);
            $manager->persist($marque);
        }

        $manager->flush();
    }
}
