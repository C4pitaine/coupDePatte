<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Form\CartType;
use Stripe\StripeClient;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Mailer\MailerInterface;

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
            $cart->setStatus('en attente');
            $salt = rand(100,100000);
            $token = md5($cart->getEmail().$salt);
            $cart->setToken($token);
            $manager->persist($cart);
            $manager->flush();

            return $this->redirectToRoute('cart_checkout',['total'=>$cart->getTotal(),'id'=>$cart->getId(),'token'=>$cart->getToken()]);
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
    #[Route('/cart/checkout/{total}/id/{id}/token/{token}',name:'cart_checkout')]
    public function checkout(string $total,int $id,string $token): Response
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
                'success_url' => "https://coupdepatte.alexandresacre.com/cart/success/".$id."/token/".$token,
                'cancel_url'=>"https://coupdepatte.alexandresacre.com/cart/cancel/".$id
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
    #[Route('/cart/success/{id}/token/{token}',name:'cart_checkout_success')]
    public function checkoutSuccess(EntityManagerInterface $manager,Cart $cart,string $token,MailerInterface $mailer):Response
    {
        if($cart){
            if($token == $cart->getToken()){
                $cart->setStatus('payé');
                $manager->persist($cart);
                $manager->flush();

                $email = (new TemplatedEmail())
                ->from("noreply@coupdepatte.alexandresacre.com")
                ->to(new Address($cart->getEmail()))
                ->subject('Facture de votre don')
                ->htmlTemplate('emails/facture.html.twig')
                ->context([
                    'donateur' => $cart->getName()." ".$cart->getFirstName(),
                    'montant' => $cart->getTotal(),
                ]);
                $mailer->send($email);
            }else{
                throw new BadRequestException('Token invalide');
            }
        }else{
            throw new BadRequestException('Id invalide');
        }

        return $this->render('cart/success.html.twig',[
            'cart' => $cart,
        ]);
    }   

    /**
     * Renvoit à la page d'annulation de la transaction
     *
     * @return Response
     */
    #[Route('/cart/cancel/{id}',name:'cart_checkout_cancel')]
    public function checkoutCancel(EntityManagerInterface $manager,Cart $cart):Response
    {
        $cart->setStatus('annulé');
        $manager->persist($cart);
        $manager->flush();

        return $this->render('cart/cancel.html.twig');
    }   
}
