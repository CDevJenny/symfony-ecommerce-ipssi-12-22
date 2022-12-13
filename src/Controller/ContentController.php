<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContentController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('content/home.html.twig', [
            'products' => $productRepository->findWithFilters(3),
        ]);
    }

    #[Route('/products', name: 'app_products', methods: ['GET'])]
    public function indexProducts(ProductRepository $productRepository): Response
    {
        return $this->render('content/product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/product/{id}', name: 'app_product_read', methods: ['GET'])]
    public function readProduct(Product $product): Response
    {
        return $this->render('content/product/read.html.twig', [
            'product' => $product,
        ]);
    }
}
