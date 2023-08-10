<?php

namespace App\Controller\Admin;

use App\Entity\Products;
use App\Form\ProductsFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/admin/produits', name: 'admin_products_')]
class ProductsController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('admin/products/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/ajout', name: 'add')]
    public function add(Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        //On crée un "nouveau produit"
        $product = new Products();

        //On crée un formulaire
        $productForm = $this->createForm(ProductsFormType::class, $product);

        //On traite la request du formulaire
        $productForm->handleRequest($request);

        //ON vérifie si le formulaire est soumis ET valide
        if($productForm->isSubmitted() && $productForm->isValid()){
            //On génère le slug
            $slug = $slugger->slug($product->getName());
            $product->setSlug($slug);

            //On arrondi le prix
            $prix = $product->getPrice() * 100;
            $product->setPrice($prix);

            //On stock
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Produit ajouté avec succès');

            //On redirige
            return $this->redirectToRoute('admin_products_index');         

        } 
        
        return $this->renderForm('admin/products/add.html.twig', compact('productForm'));
    }
    #[Route('/edition/{id}', name: 'edit')]
    public function edit(Products $product, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        //On verife si l'utilisateur peut editer avec le Voter
        $this->denyAccessUnlessGranted('PRODUCT_EDIT', $product);

        //On divise le prix par 100
        $prix = $product->getPrice() / 100;
            $product->setPrice($prix);        

        //On crée un formulaire
        $productForm = $this->createForm(ProductsFormType::class, $product);

        //On traite la request du formulaire
        $productForm->handleRequest($request);

        //ON vérifie si le formulaire est soumis ET valide
        if($productForm->isSubmitted() && $productForm->isValid()){
            //On génère le slug
            $slug = $slugger->slug($product->getName());
            $product->setSlug($slug);

            //On arrondi le prix
            $prix = $product->getPrice() * 100;
            $product->setPrice($prix);

            //On stock
            $em->persist($product);
            $em->flush();

            $this->addFlash('success', 'Modifier avec succès');

            //On redirige
            return $this->redirectToRoute('admin_products_index');         

        } 
        
        return $this->renderForm('admin/products/edit.html.twig', compact('productForm'));        
    }

    #[Route('/suppression/{id}', name: 'delete')]
    public function delete(Products $product): Response
    {
        //On verife si l'utilisateur peut supprimé avec le Voter
        $this->denyAccessUnlessGranted('PRODUCT_DELETE', $product);

        return $this->render('admin/products/index.html.twig');
    }
}
