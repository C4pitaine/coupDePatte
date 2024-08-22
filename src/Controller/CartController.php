<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Form\CartType;
use Doctrine\ORM\EntityManagerInterface;
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
        }
        
        return $this->render('cart/index.html.twig', [
            'formCart' => $form->createView(),    
        ]);
    }
}
