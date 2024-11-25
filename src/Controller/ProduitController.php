<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{
    /**
     * @Route("/produits-list", name="produit_list", methods={"GET"})
     */
    public function list(ProduitRepository $produitRepository, PaginatorInterface $paginator, Request $request)
    {
        $query = $produitRepository->createQueryBuilder('f')->getQuery();

        $produits = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('produit/list.html.twig', ['produits' => $produits]);
    }

    /**
     * @Route("/produits", name="produit_index", methods={"GET"})
     */
    public function index(ProduitRepository $produitRepository, PaginatorInterface $paginator, Request $request)
    {
        $query = $produitRepository->createQueryBuilder('f')->getQuery();

        $produits = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('produit/index.html.twig', ['produits' => $produits]);
    }

    /**
     * @Route("/produit/ajouter", name="add_produit", methods={"GET", "POST"})
     */
    public function ajouter(Request $request)
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            try {
                $em = $this->getDoctrine()->getManager();
                $em->persist($produit);
                $em->flush();

                $this->addFlash('success', 'Le produit a été ajouté avec succès!');
                return $this->redirectToRoute('produit_list');

            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de l\'ajout du produit : ' . $e->getMessage());
            }
        }

        return $this->render('produit/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/produit/{id}/edit", name="edit_produit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Produit $produit)
    {
        $form = $this->createForm(ProduitType::class, $produit);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $this->addFlash('success', 'Le produit a été mis à jour avec succès !');

            return $this->redirectToRoute('produit_list');
        }

        return $this->render('produit/edit.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit,
        ]);
    }

    /**
     * @Route("/produit/{id}/delete", name="delete_produit", methods={"POST"})
     */
    public function supprimer(Produit $produit)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($produit);
        $em->flush();

        return $this->redirectToRoute('produit_list');
    }

    /**
     * @Route("/produit/{id}/stock", name="stock_produit", methods={"GET"})
     */
    public function gestionStock(Produit $produit)
    {

        return $this->render('produit/stock.html.twig', [
            'produit' => $produit,
        ]);
    }

    /**
     * @Route("/{id}/ajouter-stock", name="ajouter_stock", methods={"POST"})
     */
    public function ajouterStock(Request $request, Produit $produit, EntityManagerInterface $em)
    {
        $quantite = $request->request->get('quantite'); // La quantité à ajouter

        if ($quantite && is_numeric($quantite) && $quantite > 0) {
            $produit->ajouterStock((int) $quantite);
            $em->persist($produit);
            $em->flush();

            $this->addFlash('success', 'Quantité ajoutée avec succès.');
        } else {
            $this->addFlash('error', 'Veuillez entrer une quantité valide.');
        }

        return $this->redirectToRoute('produit_list');
    }

    /**
     * @Route("/{id}/retirer-stock", name="retirer_stock", methods={"POST"})
     */
    public function retirerStock(Request $request, Produit $produit, EntityManagerInterface $em)
    {
        $quantite = $request->request->get('quantite'); // La quantité à retirer

        if ($quantite && is_numeric($quantite) && $quantite > 0) {
            try {
                $produit->retirerStock((int) $quantite);
                $em->persist($produit);
                $em->flush();

                $this->addFlash('success', 'Quantité retirée avec succès.');
            } catch (\Exception $e) {
                $this->addFlash('error', $e->getMessage());
            }
        } else {
            $this->addFlash('error', 'Veuillez entrer une quantité valide.');
        }

        return $this->redirectToRoute('produit_list');
    }
}
