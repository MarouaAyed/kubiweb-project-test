<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Marque;
use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Response;

class ProduitTest extends KernelTestCase
{
    public function testCreateProduct(): void
    {
          self::bootKernel();
          $container = static::getContainer();

          $produit = new Produit();
          $produit->setId(1);
          $produit->setTitre('Product 1000');
          $produit->setDescription('Description of Product 1');
          $produit->setPrixTtc(100.00);
          $produit->setQuantiteStock(10);
          $produit->setTypes('Type 1');
          $produit->setGenre('Genre 1');

          $em = $container->get(EntityManagerInterface::class);

          $marque = $em->getRepository(Marque::class)->find(1);
          if ($marque !== null) {
              $produit->setMarque($marque);
          } else {
              throw new \Exception('Marque not found');
          }
  
          $em->persist($produit);
          $em->flush();

          $errors = $container->get('validator')->validate($produit);
        
          $this->assertCount(0, $errors);
       
    }
}

