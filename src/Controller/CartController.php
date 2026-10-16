<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\ProductRepository;
use App\Repository\CategoryRepository;


final class CartController extends AbstractController
{
    public function __construct(private readonly ProductRepository $productRepository){
    }

    #[Route('/cart', name: 'app_cart')]
    public function index(SessionInterface $session, CategoryRepository $categoryRepository):
    Response
    {
        // On récupère le panier à partir de la session 'cart'
        // (on crée un tableau vide par défaut)
        $cart = $session->get('cart', []);
        $cartWithData = [];

        // On boucle dans le tableau $cart et on récupère les données de chaque produit
        foreach ($cart as $id => $quantity) {
            $product = $this->productRepository->find($id);

            // On récupère les données du produit à partir de son id
            if ($product) {
                // On ajoute le produit et la quantité au tableau $cartWithData
                $cartWithData[] = [
                'product' => $product,
                'quantity' => $quantity
                ];
            }
        }

        // Calcul du prix total du panier
        $total = array_sum(array_map(function ($item) {
            return $item['product']->getPrice() * $item['quantity'];
        }, $cartWithData));

        return $this->render('cart/index.html.twig', [
        'items' => $cartWithData,
        'total' => $total,
        'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/cart/add/{id}/', name: 'app_cart_new', methods: ['GET'])]
    public function AddToCard(int $id, SessionInterface $session): Response
    {
        // On récupère la panier à partir de la session 'cart'
        $cart = $session->get('cart', []);

        // On met à jour la quantité si le produit est déjà présent dans le panier.
        // Sinon on ajoute le produit au panier.
        if (isset($cart[$id])) {
            $cart[$id]++;
        }
        else
        {
            $cart[$id] = 1;
        }

        // On enregistre la session
        $session->set('cart', $cart);

        // Redirection vers l'affichage du panier
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/remove/{id}', name: 'app_cart_remove', methods: ['GET'])]
    public function removeFromCart(int $id, SessionInterface $session): Response
    {
        // On récupère la panier à partir de la session 'cart'
        $cart = $session->get('cart', []);

        // On vérifie que la produit existe dans le panier et on le supprime
        if (isset($cart[$id])) {
            unset($cart[$id]); // Suppression du produit à partir de l'id
            $this->addFlash('danger','Le produit a été supprimé du panier');
        }

        // mise à jour de la variable de session
        $session->set('cart', $cart);

        // Redirection vers l'affichage du panier
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/clear', name: 'app_cart_clear', methods: ['GET'])]
    public function clearCart(SessionInterface $session):Response
    {
        // Suppression de la variable de session
        $session->remove('cart');

        // Flash message pour informer l'utilisateur que la panier a été effacé
        $this->addFlash('success', 'Votre panier a été effacé.');

        // Redirection vers l'affichage du panier
        return $this->redirectToRoute('app_cart');
    }

    #[Route('/cart/subtract/{id}/', name: 'app_cart_subtract', methods: ['GET'])]
    public function SubtractToCard(int $id, SessionInterface $session): Response
    {
        // On récupère la panier à partir de la session 'cart'
        $cart = $session->get('cart', []);

        // Si la quantité à diminuer est supérieure à 1, on décrémente.
        // Sinon on retire le produit du panier
        if (isset($cart[$id]))
        {
            if ($cart[$id] > 1)
            {
                $cart[$id]--;
            }
            else
            {
                unset($cart[$id]); // Suppression du produit à partir de l'id
            }
        }

        // On enregistre la session
        $session->set('cart', $cart);

        // Redirection vers l'affichage du panier
        return $this->redirectToRoute('app_cart');
    }

}
