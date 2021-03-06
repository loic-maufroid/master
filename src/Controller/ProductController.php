<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ProductRepository;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="liste_product")
     */
    public function index(ProductRepository $productRepository)
    {
        $products = $productRepository->findAll();

        return $this->render('product/index.html.twig', [
        "products" => $products
            ]);
    }

    /**
     * @Route("/product/create", name="create_product")
     */
    public function create(Request $request, SluggerInterface $slugger)
    {
        $product = new Product();

        $form = $this->createForm(ProductType::class,$product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $product->setSlug($slugger->slug($product->getName())->lower());
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
     * @Route("/product/{slug}", name="voir_product")
     */
    public function show($slug,ProductRepository $productRepository){

        $product = $productRepository->findOneBy(["slug" => $slug]);

        if (!$product){
            throw $this->createNotFoundException("Ce produit n'existe pas");
        }

        $username = $product->getUser()->getUsername();

        return $this->render('product/voir.html.twig',
            compact('product','username'));
    }

    /**
     * @Route("/product/edit/{id}", name="edit_product")
     */
    public function edit(Request $request,Product $product){

        $this->denyAccessUnlessGranted('ROLE_ADMIN');
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

        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $entityManager = $this->getDoctrine()->getManager();

        $entityManager->remove($product);

        $entityManager->flush();

        return $this->redirectToRoute('liste_product');
    }

    /**
     * @Route("/category/{slug}",name="category_product")
     */
    public function voirCategorie($slug,CategoryRepository $categoryRepository){
        $category = $categoryRepository->findOneBy(["slug" => $slug]);

        $products = $category->getProducts();

        return $this->render("product/showByCategory.html.twig",
        compact('products','category'));
    }

    /**
     * @Route("/category",name="liste_categories")
     */
    public function liste(CategoryRepository $categoryRepository){
        $categories = $categoryRepository->findAll();

        return $this->render("product/indexCategories.html.twig",
    compact('categories'));
    }
}
