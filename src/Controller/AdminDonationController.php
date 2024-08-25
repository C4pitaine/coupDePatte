<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminDonationController extends AbstractController
{
    #[Route('/admin/donation', name: 'app_admin_donation')]
    public function index(): Response
    {
        return $this->render('admin_donation/index.html.twig', [
            'controller_name' => 'AdminDonationController',
        ]);
    }
}
