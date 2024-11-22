<?php

namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Marque;
use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;

class ProduitTest extends ApiTestCase
{
    public function testCreateProduct()
    {
        self::bootKernel();
        $container = self::$container;
        $client = $container->get('test.client');  // Obtient le client HTTP de test
        $client->disableReboot();  // Permet de garder la même instance de client entre les appels
        $em = $container->get(EntityManagerInterface::class);

        $produit = new Produit();
        $produit->setId(20);
        $produit->setTitre('Product 1');
        $produit->setDescription('Description of Product 1');
        $produit->setPrixTtc(100.00);
        $produit->setQuantiteStock(10);
        $produit->setTypes('Type 1');
        $produit->setGenre('Genre 1');

        $marque = $em->getRepository(Marque::class)->find(1);

        if ($marque !== null) {
            $produit->setMarque($marque);
        } else {
            throw new \Exception('Marque not found');
        }


        $em->persist($produit);
        $em->flush();

        $client->request('POST', '/api/produits', [
            'json' => [
                'id' => 20,
                'titre' => 'Product 1',
                'description' => 'Description of Product 1',
                'prixTtc' => 100.00,
                'quantiteStock' => 10,
                'types' => 'Type 1',
                'genre' => 'Genre 1',
                'marque_id' => 1
            ]
        ]);


        $em->getRepository(Produit::class)->find(20);

        // Vérifier que la réponse est correcte (201 Created)
        $this->assertEquals(Response::HTTP_CREATED, $client->getResponse()->getStatusCode());
    }

}
