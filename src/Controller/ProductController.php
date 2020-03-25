<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="liste_product")
     */
    public function index(ProductRepository $productRepository)
    {
        $products = $productRepository->findAll();

        dump($products);
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

    /**
     * @Route("/product/{id}", name="voir_product")
     */
    public function show($id,ProductRepository $productRepository){

        $product = $productRepository->find($id);

        if (!$product){
            throw $this->createNotFoundException("Ce produit n'existe pas");
        }

        return $this->render('product/voir.html.twig',[
            'product' => $product
        ]);
    }

    /**
     * @Route("/product/edit/{id}", name="edit_product")
     */
    public function edit(Request $request,Product $product){

        $form = $this->createForm(ProductType::class,$product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('liste_product');
        }

    return $this->render('product/edit.html.twig',[
        'form' => $form->createView()
    ]);
    }

    /**
     * @Route("/product/delete/{id}", name="delete_product")
     */
    public function delete(Product $product){

        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->remove($product);

        $entityManager->flush();

        return $this->redirectToRoute('liste_product');
    }
}
