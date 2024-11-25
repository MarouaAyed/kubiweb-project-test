<?php

namespace App\Controller;


use App\Entity\Client;
use App\Form\ClientType;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Services;

class ClientController extends AbstractController
{
    /**
     * @Route("/inscription", name="client_inscription")
     */
    public function inscription(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher,
        JWTTokenManagerInterface $jwtManager
    ): Response {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $defaultPassword = '123456';
            $hashedPassword = $passwordHasher->hashPassword($client, $defaultPassword);
            $client->setPassword($hashedPassword);

            $entityManager->persist($client);
            $entityManager->flush();

            $this->addFlash('success', 'Inscription réussie !');
            return $this->redirectToRoute('client_inscription');
        }

        return $this->render('client/inscription.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
