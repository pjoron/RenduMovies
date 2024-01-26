<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


class ProductController extends AbstractController
{
    /**
     * @Route("/products", name="products_list")
     */
    public function listProducts()
    {
        return $this->render('product/list.html.twig', [
            'title' => 'Liste des produits',
        ]);
    }

    /**
     * @Route("/product/{id}", name="product_view")
     */
    public function viewProduct(Request $request, $id)
    {
        return $this->render('product/view.html.twig', [
            'title' => 'Affichage du produit ' . $id,
        ]);
    }
}
