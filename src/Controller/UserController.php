<?php

namespace App\Controller;

use Error;
use App\Entity\Cart;
use App\Entity\CartsProducts;
use App\Entity\User;
use App\Form\UserType;
use DateTimeImmutable;
use Stripe\StripeClient;
use App\Form\UserPasswordType;
use App\Form\RegistrationFormType;
use App\Repository\CartsProductsRepository;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class UserController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $cart = new Cart();
            $user->setCart($cart);
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('security/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/profile/{id}', name: 'app_profile')]
    public function indexProfile(User $user, Request $request, PaginatorInterface $paginator)
    {
        $userProducts = $user->getProducts()->toArray();

        $products = $paginator->paginate(
            $userProducts, // Requête contenant les données à paginer (ici nos articles)
            $request->query->getInt('page', 1), // Numéro de la page en cours, passé dans l'URL, 1 si aucune page
            4 // Nombre de résultats par page
        );

        return $this->render('security/profile/index.html.twig', [
            'user' => $user,
            'products' => $products
        ]);
    }

    #[Route('/profile/{id}/update', name: 'app_user_update', methods: ['GET', 'POST'])]
    public function updateProfile(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUpdatedAt(new DateTimeImmutable('now'));
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_profile', ["id" => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('security/profile/update.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/profile/{id}/password', name: 'app_user_password', methods: ['GET', 'POST'])]
    public function updatePassword(Request $request, User $user, UserRepository $userRepository): Response
    {
        $form = $this->createForm(UserPasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setUpdatedAt(new DateTimeImmutable('now'));
            $userRepository->save($user, true);

            return $this->redirectToRoute('app_profile', ["id" => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('security/profile/password.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route(path: '/profile/{id}/cart', name: 'app_profile_cart')]
    public function readCart(User $user)
    {
        $cart = $user->getCart();
        $products = $cart->getCartsProducts()->toArray();

        $quantities = [];
        foreach ($products as $product) {
            array_push($quantities, $product->getQuantity() * $product->getProduct()->getPrice());
        }
        $totalPrice = array_sum($quantities);
        return $this->render('security/profile/cart.html.twig', [
            'user' => $user,
            'products' => $products,
            'total' => $totalPrice
        ]);
    }
    #[Route('/profile/cart/item-delete/{id}', name: 'app_cart_item_delete')]
    public function deleteCartItem(CartsProducts $cartsProducts, CartsProductsRepository $cpRepository)
    {
        $cpRepository->remove($cartsProducts, true);

        return $this->redirectToRoute('app_profile_cart', ['id' => $this->getUser()->getId()]);
    }

    #[Route('/profile/{id}/checkout={total}', name: 'app_profile_checkout')]
    public function cartCheckout(User $user, $total)
    {
        $currentUser = $this->getUser()->getId();
        if ($currentUser !== $user->getId()) {
            return $this->redirectToRoute('app_profile', ['id' => $currentUser]);
        }

        $stripe = new \Stripe\StripeClient($this->getParameter('stripe_sk'));

        $stripe->paymentIntents->create(
            [
                'amount' => $total,
                'currency' => 'eur',
                'automatic_payment_methods' => ['enabled' => true],
            ]
        );



        return $this->render('security/profile/checkout.html.twig');
    }
}
