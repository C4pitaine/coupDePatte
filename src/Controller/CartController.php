<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Form\CartType;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\StripeClient;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{   


    #[Route('/cart', name: 'cart')]
    public function index(EntityManagerInterface $manager,Request $request): Response
    {
        $cart = new Cart();
        $form = $this->createForm(CartType::class,$cart);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($cart);
            $manager->flush();

            return $this->redirectToRoute('cart_checkout',['total'=>$cart->getTotal(),'id'=>$cart->getId()]);
        }
        
        return $this->render('cart/index.html.twig', [
            'formCart' => $form->createView(),    
        ]);
    }

    #[Route('/cart/checkout/{total}/id/{id}',name:'cart_checkout')]
    public function checkout(string $total,int $id): Response
    {
        $gateway = new StripeClient($_ENV['STRIPE_SECRETKEY']);
        $amount = intval($total)*100;

        $checkout = $gateway->checkout->sessions->create(
            [
                'line_items'=>[[
                    'price_data'=>[
                        'currency'=>$_ENV['STRIPE_CURRENCY'],
                        'product_data'=>[
                            'name'=>'Friandises',
                        ],
                        'unit_amount'=>$amount,
                    ],
                    'quantity'=>'1'
                ]],
                'mode'=>'payment',
                'success_url' => "http://127.0.0.1:8000/cart/success/".$id,
                'cancel_url'=>"http://127.0.0.1:8000/cart/cancel"
            ]);

            return $this->redirect($checkout->url);

        return $this->render('cart/checkout.html.twig',[

        ]);
    }

    #[Route('/cart/success/{id}',name:'cart_checkout_success')]
    public function checkoutSuccess(Request $request,Cart $cart):Response
    {
        return $this->render('cart/success.html.twig',[

        ]);
    }   

    #[Route('/cart/cancel',name:'cart_checkout_cancel')]
    public function checkoutCancel(Request $request):Response
    {
        return $this->render('cart/cancel.html.twig',[

        ]);
    }   
}
