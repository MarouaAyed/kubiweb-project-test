<?php

namespace App\Controller;

use App\Entity\Client;
use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommandeController extends AbstractController
{
    /**
     * @Route("/mes-commandes", name="commande_index")
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        //  $user = $this->getUser();

        $userRepository = $entityManager->getRepository(Client::class);

        // Récupérer le premier utilisateur de la base de données
        $user = $userRepository->findOneBy([], ['id' => 'ASC']);

        $commandes = $this->getDoctrine()
            ->getRepository(Commande::class)
            ->findBy(['client' => $user]);

        return $this->render('commande/index.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    /**
     * @Route("/ma-commande", name="commande_client")
     */
    public function commande_client(EntityManagerInterface $entityManager): Response
    {
        $commandeRepository = $entityManager->getRepository(Commande::class);

        //  $user = $this->getUser();

        $userRepository = $entityManager->getRepository(Client::class);

        $user = $userRepository->findOneBy([], ['id' => 'ASC']);

        $commande = $commandeRepository->findActiveCommande($user);


        return $this->render('commande/client.html.twig', [
            'commande' => $commande,
        ]);
    }

    /**
     * @Route("/commander/{id}", name="commander")
     */
    public function commander(Produit $produit, EntityManagerInterface $entityManager)
    {
        $commandeRepository = $entityManager->getRepository(Commande::class);

        //  $user = $this->getUser();

        $userRepository = $entityManager->getRepository(Client::class);

        $user = $userRepository->findOneBy([], ['id' => 'DESC']);

        //dd(  $user);
        if (!$user) {
            throw new \Exception("Utilisateur non connecté");
        }

        $commande = $commandeRepository->findActiveCommande($user);


        if (!$commande) {

            $yearMonth = (new \DateTime())->format('m-Y');

    // Chercher la dernière commande pour ce mois
    $lastCommande = $commandeRepository->findLastCommandeOfMonth($yearMonth);

    // Extraire et incrémenter le numéro de commande si nécessaire
    if ($lastCommande) {
        $lastNumCmd = (int) substr($lastCommande->getNumCmd(), 3, 4); // Extraire le numéro de la commande
        $nextNum = $lastNumCmd + 1;
    } else {
        $nextNum = 1; // Si aucune commande n'existe, commencer à 1
    }

    // Créer un nouveau numéro de commande
    $numCmd = 'CMD' . str_pad($nextNum, 4, '0', STR_PAD_LEFT) . '-' . $yearMonth;

            $commande = new Commande();
            $commande->setNumCmd($numCmd);

            $commande->setDateCommande(new \DateTime());
            $commande->setClient($user);
        }

        $ligneCommande = $commande->getLignes()->filter(function ($ligne) use ($produit) {
            return $ligne->getProduit() === $produit;
        })->first();

        if ($ligneCommande) {
            $ligneCommande->setQuantite($ligneCommande->getQuantite() + 1);
        } else {
            $ligneCommande = new LigneCommande($produit, $commande, 1);

            $commande->addLigne($ligneCommande);
        }

        $total = 0;
        foreach ($commande->getLignes() as $ligne) {
            $total += $ligne->getProduit()->getPrixTtc() * $ligne->getQuantite();
        }
        $commande->setTotal($total);

        $entityManager->persist($commande);
        $entityManager->flush();

        return $this->redirectToRoute('commande_client');
    }

/**
 * @Route("/commande/update_quantite/{id}", name="update_quantite", methods={"POST"})
 */
public function updateQuantite(Request $request, LigneCommande $ligneCommande)
{
    $quantite = $request->request->get('quantite');
    
    if ($quantite && $quantite > 0) {
        $produit = $ligneCommande->getProduit();
        $stockDisponible = $produit->getQuantiteStock();

        // Vérifier que la nouvelle quantité ne dépasse pas le stock disponible
        if ($quantite <= $stockDisponible) {
            $ancienneQuantite = $ligneCommande->getQuantite();
            $differenceQuantite = $quantite - $ancienneQuantite;

            $ligneCommande->setQuantite($quantite);

            $produit->setQuantiteStock($stockDisponible - $differenceQuantite);

            $prixTotalLigne = $ligneCommande->getProduit()->getPrixTtc() * $quantite;
            $commande = $ligneCommande->getCommande();
            $commande->setTotal($commande->getTotal() - $ligneCommande->getProduit()->getPrixTtc() * $ancienneQuantite + $prixTotalLigne);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();
        } else {
            $this->addFlash('error', 'La quantité demandée dépasse le stock disponible.');
        }
    } else {
        // Si la quantité est invalide (inférieure ou égale à 0)
        $this->addFlash('error', 'Quantité invalide.');
    }

    // Rediriger vers la page de commande du client
    return $this->redirectToRoute('commande_client');
}


    /**
     * @Route("/commande/delete_ligneCommande/{id}", name="delete_ligneCommande", methods={"POST"})
     */
    public function delete_ligneCommande(LigneCommande $ligneCommande)
    {
        $entityManager = $this->getDoctrine()->getManager();

        $commande = $ligneCommande->getCommande();

        $produit = $ligneCommande->getProduit();

        $produit->setQuantiteStock($produit->getQuantiteStock() + $ligneCommande->getQuantite());

        $commande->removeLigne($ligneCommande);

        $entityManager->remove($ligneCommande);

        $entityManager->flush();

        return $this->redirectToRoute('commande_client');
    }


    /**
 * @Route("/commande/valider/{id}", name="valider_commande", methods={"POST"})
 */
public function validerCommande(Commande $commande)
{
    if ($commande && $commande->getStatus() !== 'validée') {
        $commande->setStatus('validée');
        
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();

        $this->addFlash('success', 'Votre commande a été validée avec succès.');
    } else {
        $this->addFlash('error', 'Erreur : la commande est déjà validée ou introuvable.');
    }

    return $this->redirectToRoute('commande_index');
}

/**
 * @Route("/commande/details/{id}", name="commande_details")
 */
public function detailsCommande($id)
{
    // Vous récupérez la commande par son ID
    $commande = $this->getDoctrine()->getRepository(Commande::class)->find($id);

    if (!$commande) {
        throw $this->createNotFoundException('Commande non trouvée');
    }

    // Vous récupérez les lignes de commande associées
    $lignes = $commande->getLignes();

    return $this->render('commande/details.html.twig', [
        'commande' => $commande,
        'lignes' => $lignes
    ]);
}


}
