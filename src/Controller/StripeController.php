<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\Product;
use Stripe\Checkout\Session;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



class StripeController extends AbstractController
{
    /**
     * @Route("/commande/create-session/{reference}", name="/stripe_create_session")
     */
    public function index(EntityManagerInterface $entityManager,Cart $cart,$reference)
    {
        //mon tableau que je passe dans stripe
        $products_for_stripe = [];
        //mon url        
        $YOUR_DOMAIN = 'http://127.0.0.1:8000/';

        $order=$entityManager->getRepository(Order::class)->findOneByReference($reference);
        
       if(!$order){
        return $this->redirectToRoute('app_order');
    }

        foreach ($order->getOrderDetails()->getValues() as $product) {
           $product_object = $entityManager->getRepository(Product::class)->findOneByName($product->getProduct());
            $products_for_stripe[] = [
                'price_data' => [
                    'currency' => 'EUR',
                    'unit_amount' => $product->getPrice(),
                    'product_data' => [
                        'name' => $product->getProduct(),
                        'images' => [$YOUR_DOMAIN ."/uploads/".$product_object->getIllustration()],

                    ],
                ],

                'quantity' => $product->getQuantity(),
            ];
           
        }
        //dd($products_for_stripe);


        $products_for_stripe[] = [
            'price_data' => [
                'currency' => 'EUR',
                'unit_amount' => $order->getCarrierPrice(),
                'product_data' => [
                    'name' => $order->getCarrierName(),
                    'images' =>[$YOUR_DOMAIN], 

                ],
            ],

            'quantity' => 1,
        ];


        //connection a l api

        Stripe::setApiKey("sk_test_51KlYImEXkeUvYtMtyiWkB3h5RcFW96RokV1yylhq9NeVeGUXWJDjHS5rR3hOcLcgcH0gJVVk7vJQm4Q5tcf8jZDI002lv4FDqX");

        $checkout_session = Session::create([
            'customer_email'=>$this->getUser()->getEmail(),
            'payment_method_types' => ['card'],
            'line_items' => [
                $products_for_stripe
            ],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . 'commande/merci/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . 'commande/erreur/{CHECKOUT_SESSION_ID}',
                
        ]);
        $order->setStripeSessionId($checkout_session->id);
        $entityManager->flush();

        return $this->redirect($checkout_session->url);
      
    }
}
