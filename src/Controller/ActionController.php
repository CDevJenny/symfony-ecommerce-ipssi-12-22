<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\Product;
use App\Form\ProductType;
use App\Entity\CartsProducts;
use Doctrine\ORM\Mapping\Entity;
use App\Repository\ProductRepository;
use Doctrine\Persistence\ObjectRepository;
use App\Repository\CartsProductsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ActionController extends AbstractController
{
    #[Route('/product/create', name: 'app_product_create', methods: ['GET', 'POST'])]
    public function createProduct(Request $request, ProductRepository $productRepository): Response
    {
        $test = Product::class;
        $user = $this->getUser();
        $product = new $test();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setSeller($user);
            $productRepository->save($product, true);

            return $this->redirectToRoute('app_products', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('content/product/create.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/product/{id}/update', name: 'app_product_update', methods: ['GET', 'POST'])]
    public function updateProduct(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product->setUpdatedAt(new DateTimeImmutable('now'));
            $productRepository->save($product, true);

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('content/product/update.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/product/delete/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function deleteProduct(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);
        }

        return $this->redirectToRoute('app_products', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/product/add/{id}', name: 'app_product_add', methods: ['GET', 'POST'])]
    public function addProductToCart(Request $request, Product $product, CartsProductsRepository $cpRepository): Response
    {
        /** @var User $user **/
        $user = $this->getUser();
        $cart = $user->getCart();
        $cp = $cart->getCartsProducts()->toArray();


        if (empty($cp)) {
            $cProduct = new CartsProducts();
            $cProduct->setCart($cart);
            $cProduct->setProduct($product);
            $cProduct->setQuantity(1);
        } else {
            foreach ($cp as $cProduct) {
                $cProductId = $cProduct->getProduct()->getId();
                if ($cProductId == $product->getId()) {
                    $q = $cProduct->getQuantity();
                    $cProduct->setQuantity($q + 1);

                    break;
                } else {
                    $cProduct = new CartsProducts();
                    $cProduct->setCart($cart);
                    $cProduct->setProduct($product);
                    $cProduct->setQuantity(1);
                }
            }
        }
        $cpRepository->save($cProduct, true);
        return $this->redirectToRoute('app_products', [], Response::HTTP_SEE_OTHER);
    }
}
