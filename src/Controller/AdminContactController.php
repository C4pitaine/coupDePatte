<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminContactController extends AbstractController
{
    #[Route('/admin/contact', name: 'app_admin_contact')]
    public function index(): Response
    {
        return $this->render('admin_contact/index.html.twig', [
            'controller_name' => 'AdminContactController',
        ]);
    }
}
