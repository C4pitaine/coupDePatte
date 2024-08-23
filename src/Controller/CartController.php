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

    /**
     * Affichage et traitement du panier
     *
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    #[Route('/cart', name: 'cart')]
    public function index(EntityManagerInterface $manager,Request $request): Response
    {
        $cart = new Cart();
        $form = $this->createForm(CartType::class,$cart);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $cart->setStatus(false);
            $manager->persist($cart);
            $manager->flush();

            return $this->redirectToRoute('cart_checkout',['total'=>$cart->getTotal(),'id'=>$cart->getId()]);
        }
        
        return $this->render('cart/index.html.twig', [
            'formCart' => $form->createView(),    
        ]);
    }

     /**
     * Transaction Stripe
     *
     * @param string $total
     * @param integer $id
     * @return Response
     */
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

    /**
     * Page de réussite de transaction
     *
     * @param Request $request
     * @param Cart $cart
     * @return Response
     */
    #[Route('/cart/success/{id}',name:'cart_checkout_success')]
    public function checkoutSuccess(EntityManagerInterface $manager,Cart $cart):Response
    {
        $cart->setStatus(true);
        $manager->persist($cart);
        $manager->flush();

        return $this->render('cart/success.html.twig',[
            'cart' => $cart,
        ]);
    }   

    /**
     * Renvoit à la page d'annulation de la transaction
     *
     * @return Response
     */
    #[Route('/cart/cancel',name:'cart_checkout_cancel')]
    public function checkoutCancel():Response
    {
        return $this->render('cart/cancel.html.twig');
    }   
}
