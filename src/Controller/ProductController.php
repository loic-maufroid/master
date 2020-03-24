<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="liste_product")
     */
    public function index()
    {
        $products=null;
        return $this->render('product/index.html.twig', [
        "products" => $products
            ]);
    }

    /**
     * @Route("/product/create", name="create_product")
     */
    public function create(Request $request)
    {
        $product = new Product();

        $form = $this->createForm(ProductType::class,$product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // On peut aussi utiliser l'autowiring :
            // create(EntityManagerInterface $entityManager)
            $entityManager = $this->getDoctrine()->getManager();

            // On demande à Doctrine de mettre l'objet en attente
            $entityManager->persist($product);

            // Exécute la(es) requête(s) (INSERT...)
            $entityManager->flush();
            
            $product = new Product();

            $form = $this->createForm(ProductType::class,$product);

            $this->addFlash('success','Produit bien ajouté !');
            // return $this->redirectToRoute('product_list');
        }

        return $this->render('product/create.html.twig', [
        "form" => $form->createView()
            ]);
    }
}
