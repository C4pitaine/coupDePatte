<?php

namespace App\Controller;

use App\Repository\AdoptionRepository;
use App\Repository\CartRepository;
use App\Repository\ContactRepository;
use App\Repository\DonationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminDashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard_index')]
    public function index(ContactRepository $contactRepo,AdoptionRepository $adoptionRepo,UserRepository $userRepo,DonationRepository $donationRepo,CartRepository $cartRepo): Response
    {
        $messageNotSeen = $contactRepo->findBy(['status'=>'en attente']);
        $adoption = $adoptionRepo->findAll();
        $user = $userRepo->findAll();
       
        $donations = $donationRepo->findBy(['status'=>'payé']);
        $totalDonation = 0;
        foreach($donations as $donation){
            $totalDonation+=  $donation->getMontant();
        }

        $carts = $cartRepo->findBy(['status'=>'payé']);
        $totalCart = 0;
        foreach($carts as $cart){
            $totalCart+= $cart->getTotal();
        }


        return $this->render('admin/dashboard/index.html.twig', [
            'messageNotSeen' => Count($messageNotSeen),
            'adoption' => Count($adoption),
            'user' => Count($user)-1,
            'totalDonation' => $totalDonation,
            'totalCart' => $totalCart,
        ]);
    }
}
