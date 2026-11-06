<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Order;
use App\Entity\OrderProducts;
use App\Form\OrderType;
use App\Repository\CategoryRepository;
use App\Repository\CityRepository;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;



final class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order')]
    public function index(Request $request, CategoryRepository $categoryRepository,
    ProductRepository $productRepository, SessionInterface $session,
    EntityManagerInterface $entityManager): Response
    {

        $cart = $session->get('cart', []);
        $cartWithData = [];

        // On boucle dans le tableau $cart et on récupère les données de chaque produit
        foreach ($cart as $id => $quantity) {
            $product = $productRepository->find($id);

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

        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            if(!empty($cart))
            {
                $order->setCreatedAt(new \DateTimeImmutable());
                $order->setTotalPrice($total +  $order->getShippingPrice());

                $entityManager->persist($order);
                $entityManager->flush();

                foreach ($cart as $id => $quantity) {
                    $product = $productRepository->find($id);

                        if ($product) {
                            $orderProduct = new OrderProducts();
                            $orderProduct->setOrder($order);
                            $orderProduct->setProduct($product);
                            $orderProduct->setQte($quantity);
                            $orderProduct->setPrice($product->getPrice());

                            $entityManager->persist($orderProduct);
                            $entityManager->flush();
                        }
                }
            $session->remove('cart'); //On vide le panier
            return $this->redirectToRoute('order_message', [], Response::HTTP_SEE_OTHER);
            }
            else
            {
                //message commande annulee
                $this->addFlash('danger','Impossible de commander, le panier est vide !');
                return $this->redirectToRoute('app_cart', [], Response::HTTP_SEE_OTHER);
            }
        }


        return $this->render('order/index.html.twig', [
            'controller_name' => 'OrderController',
            'form' => $form,
            'total' =>$total,
            'categories' => $categoryRepository->findAll(),
        ]);
    }
    #[Route('/get_shipping_cost', name: 'get_shipping_cost', methods: ['POST'])]
    public function getShippingCost(Request $request, CityRepository $cityRepository): JsonResponse
    {
        $id = $request->request->get('city');
        $city = $cityRepository->find($id);
        $cost = $city?->getShippingCost() ?? 10.00; //10€ par défaut

        return new JsonResponse(['shippingCost' => $cost]);
    }
    #[Route('/order-message', name: 'order_message')]
    public function orderMessage(CategoryRepository $categoryRepository): Response
    {
        return $this->render('order/order-message.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }
    #[Route('/editor/orders', name: 'app_orders')]
    public function getAllOrders(OrderRepository $orderRepository, CategoryRepository $categoryRepository): Response
    {     
        $orders = $orderRepository->findAll();
        return $this->render('order/orders.html.twig', [
            'categories' => $categoryRepository->findAll(),
            'orders' =>$orders
        ]);
    }
    #[Route('/editor/order/{id}/is-delivered/update', name: 'app_order_is_delivered_update')]
    public function isDeliveredUpdate($id, OrderRepository $orderRepository,
    EntityManagerInterface $entityManager): Response
    {
        $order = $orderRepository->find($id);
        if (!$order) {
            throw $this->createNotFoundException('Commande introuvable');
        }
        $order->setIsDelivered(true);
        $entityManager->flush();

        $this->addFlash('success','La commande a été marquée comme livrée.');

        return $this->redirectToRoute('app_orders', [], Response::HTTP_SEE_OTHER);
    }





}
