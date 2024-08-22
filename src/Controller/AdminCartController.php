<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Form\SearchType;
use App\Service\PaginationService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCartController extends AbstractController
{
    /**
     * Permet d'afficher les commandes de friandises avec une recherche sur les noms / prénoms
     *
     * @param Request $request
     * @param PaginationService $pagination
     * @param integer $page
     * @param string $recherche
     * @return Response
     */
    #[Route('/admin/cart/{page<\d+>?1}/{recherche}', name: 'admin_cart_index')]
    public function index(Request $request,PaginationService $pagination,int $page,string $recherche=""): Response
    {
        $pagination->setEntityClass(Cart::class)
                    ->setSearch($recherche)
                    ->setPage($page)
                    ->setLimit(10);

        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $recherche = $form->get('search')->getData();
            if($recherche !== null){
                $pagination->setSearch($recherche)
                        ->setPage(1);
            }else{
                $pagination->setSearch("")
                        ->setPage(1);
            }
        }

        return $this->render('admin/cart/index.html.twig', [
            'pagination' => $pagination,
            'formSearch' => $form->createView(),
        ]);
    }
}
