<?php

namespace App\Controller\Api;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{
     /**
     * @Route("/api/products", name="create_or_update_product", methods={"POST"})
     */
    public function createOrUpdateProduct(Request $request, ProduitRepository $produitRepository, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $produit = $produitRepository->find($data['id']) ?? new Produit();

        $types = isset($data['types']) && is_string($data['types']) ? explode(',', $data['types']) : (array) $data['types'];

        if (empty($types)) {
            $types = ['default'];  
        }
        $produit->setTitre($data['titre'])
                ->setDescription($data['description'])
                ->setQuantiteStock($data['quantiteStock'])
                ->setPrixTtc($data['prixTtc'])
                ->setTypesFromArray($types)
                ->setGenre($data['genre']);

        $entityManager->persist($produit);
        $entityManager->flush();

        return new JsonResponse(['status' => 'Product created'], Response::HTTP_CREATED);
    }
}
