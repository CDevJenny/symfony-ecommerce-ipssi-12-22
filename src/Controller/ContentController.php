<?php

namespace App\Controller;

use App\Entity\CartsProducts;
use App\Entity\Product;
use App\Form\AddCartType;
use App\Repository\CartsProductsRepository;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
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
    public function indexProducts(ProductRepository $productRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $allProducts = $productRepository->findAll();

        $products = $paginator->paginate(
            $allProducts, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            9 // Nombre de résultats par page
        );
        return $this->render('content/product/index.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('/product/{id}', name: 'app_product_read', methods: ['GET', 'POST'])]
    public function readProduct(Request $request, Product $product, CartsProductsRepository $cpRepository): Response
    {
        /** @var User $user **/
        $user = $this->getUser();
        $cart = $user->getCart();

        $cp = $cart->getCartsProducts()->toArray();
        $form = $this->createForm(AddCartType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $formQuantity = $form->get('quantity')->getData();
            if (empty($cp)) {
                $cProduct = new CartsProducts();
                $cProduct->setCart($cart);
                $cProduct->setProduct($product);
                $cProduct->setQuantity($formQuantity);
            } else {
                foreach ($cp as $cProduct) {
                    $cProductId = $cProduct->getProduct()->getId();
                    if ($cProductId == $product->getId()) {
                        $q = $cProduct->getQuantity();
                        $cProduct->setQuantity($q + $formQuantity);

                        break;
                    } else {
                        $cProduct = new CartsProducts();
                        $cProduct->setCart($cart);
                        $cProduct->setProduct($product);
                        $cProduct->setQuantity($formQuantity);
                    }
                }
            }
            $cpRepository->save($cProduct, true);
            return $this->redirectToRoute('app_products');
        }
        return $this->render('content/product/read.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }
}
