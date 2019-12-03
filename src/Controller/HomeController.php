<?php

namespace App\Controller;

use App\Entity\Client;
use App\Form\ClientType;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index()
    {
        $repository = $this->getDoctrine()->getRepository(Client::class);
        $categories = $repository->findAll();
        return $this->render('home/index.html.twig', [
            "categories" => $categories,
        ]);
    }

    /**
     * @Route("/home/ajouter", name="ajouter")
     */
    public function ajouter(Request $request)
    {
        $categorie = new Client();
        //creation du formulaire
        $formulaire = $this->createForm(ClientType::class, $categorie);
        $formulaire->handleRequest($request);
        if ($formulaire->isSubmitted() && $formulaire->isValid()) {
            //récuperer l'entity manager (sorte de connexion à la BDD
            $em = $this->getDoctrine()->getManager();
            //je dis au manager que je veux ajouter un client dans la BDD
            $em->persist($categorie);
            $em->flush();
            return $this->redirectToRoute("home");
        }
        return $this->render('home/formulaire.html.twig', [
            "formulaire" => $formulaire->createView(),
            "h1" => "Ajouter un clientapp ",
        ]);
    }

    /**
     * @Route("/home/modifier/{id}", name="modifier")
     */
    public function modifier(int $id, Request $request)
    {
        $repository = $this->getDoctrine()->getRepository(Client::class);
        $categorie = $repository->find($id);
        //creation du formulaire
        $formulaire = $this->createForm(ClientType::class, $categorie);
        $formulaire->handleRequest($request);
        if ($formulaire->isSubmitted() && $formulaire->isValid()) {
            //récuperer l'entity manager (sorte de connexion à la BDD
            $em = $this->getDoctrine()->getManager();
            //je dis au manager que je veux ajouter la categorie dans la BDD
            $em->persist($categorie);
            $em->flush();
            return $this->redirectToRoute("home");
        }
        return $this->render('home/formulaire.html.twig', [
            "formulaire" => $formulaire->createView(),
            "h1" => "Modification du client <i>" . $categorie->getId() . "</i>",
        ]);
    }

    /**
     * @Route("/home/supprimer/{id}", name="supprimer")
     */
    public function delete(Request $request, $id)
    {
        $repository = $this->getDoctrine()->getRepository(Client::class);
        $categorie = $repository->find($id);
        $formulaire = $this->createFormBuilder()
            ->add("submit", SubmitType::class, ["label" => "OK", "attr" => ["class" => "btn btn-success"]])
            ->getForm();
        $formulaire->handleRequest($request);
        if ($formulaire->isSubmitted() && $formulaire->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($categorie);
            $em->flush();
            return $this->redirectToRoute("home");
        }
        return $this->render('home/formulaire.html.twig', [
            'controller_name' => 'HomeController',
            'formulaire' => $formulaire->createView(),
            "h1" => "Supprimer le client <i>" . $categorie->getId() . "</i>"
        ]);
    }
}