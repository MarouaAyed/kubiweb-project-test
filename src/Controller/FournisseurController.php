<?php

namespace App\Controller;

use App\Entity\Fournisseur;
use App\Form\FournisseurType;
use App\Repository\FournisseurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class FournisseurController extends AbstractController
{
    /**
     * @Route("/fournisseurs", name="fournisseur_index", methods={"GET"})
     */
    public function index(FournisseurRepository $fournisseurRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $fournisseurRepository->createQueryBuilder('f')->getQuery();

        $fournisseurs = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('fournisseur/index.html.twig', [
            'fournisseurs' => $fournisseurs,
        ]);
    }

    /**
     * @Route("/fournisseur/add", name="add_fournisseur", methods={"GET", "POST"})
     */
    public function ajouter(Request $request, EntityManagerInterface $em): Response
    {
        $fournisseur = new Fournisseur();
        $form = $this->createForm(FournisseurType::class, $fournisseur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $marque = $fournisseur->getMarque();

            try {
                if ($marque) {
                    $marque->setFournisseur($fournisseur);
                }

                $em->persist($fournisseur);
                if ($marque) {
                    $em->persist($marque);
                }

                $em->flush();

                return $this->redirectToRoute('fournisseur_index');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la création du fournisseur : ' . $e->getMessage());
            }
        }

        return $this->render('fournisseur/ajouter.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/fournisseur/{id}/edit", name="edit_fournisseur", methods={"GET", "POST"})
     */
    public function edit(Request $request, Fournisseur $fournisseur, EntityManagerInterface $em): Response
    {
        $marque = $fournisseur->getMarque(); // ancien marque
        if ($marque) {
            $marque->setFournisseur(null);
            $em->persist($marque);
        }
        $form = $this->createForm(FournisseurType::class, $fournisseur);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {


            $marque = $fournisseur->getMarque(); // new marque

            try {
                if ($marque) {
                    $marque->setFournisseur($fournisseur);
                }
                $em->persist($fournisseur);
                if ($marque) {
                    $em->persist($marque);
                }

                $em->flush();

                $this->addFlash('success', 'Fournisseur mis à jour avec succès.');

                return $this->redirectToRoute('fournisseur_index');
            } catch (\Exception $e) {
                // Gérer l'exception et afficher un message d'erreur
                $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour du fournisseur : ' . $e->getMessage());
            }
        }

        return $this->render('fournisseur/edit.html.twig', [
            'form' => $form->createView(),
            'fournisseur' => $fournisseur,
        ]);
    }

    /**
     * @Route("/fournisseur/{id}/delete", name="delete_fournisseur", methods={"POST"})
     */
    public function delete(Request $request, Fournisseur $fournisseur, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $fournisseur->getId(), $request->get('_token'))) {
            $marque = $fournisseur->getMarque();

            if ($marque) {
                $marque->setFournisseur(null);
                $em->persist($marque);
            }

            $em->remove($fournisseur);
            $em->flush();

            $this->addFlash('success', 'Fournisseur supprimé avec succès.');
        }

        return $this->redirectToRoute('fournisseur_index');
    }

}
