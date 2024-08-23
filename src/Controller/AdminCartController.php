<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Form\SearchType;
use App\Form\SearchFiltreCartType;
use App\Service\PaginationService;
use App\Service\PaginationFiltreService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCartController extends AbstractController
{

    /**
     * Permet de supprimer une commande
     *
     * @param EntityManagerInterface $manager
     * @param Cart $cart
     * @return Response
     */
    #[Route('/admin/cart/{id}/delete',name:'admin_cart_delete')]
    public function delete(EntityManagerInterface $manager,Cart $cart): Response
    {
        $this->addFlash('danger','La commande de friandises de '.$cart->getName().' '.$cart->getFirstName().' a bien été supprimé');
        $manager->remove($cart);
        $manager->flush();
        return $this->redirectToRoute('admin_cart_index');
    }

    /**
     * Permet d'afficher les commandes de friandises avec une recherche sur les noms / prénoms
     *
     * @param Request $request
     * @param PaginationFiltreService $pagination
     * @param integer $page
     * @param string $recherche
     * @return Response
     */
    #[Route('/admin/cart/{page<\d+>?1}/recherche/{recherche}/filtre/{filtre}', name: 'admin_cart_index')]
    public function index(Request $request,PaginationFiltreService $pagination,int $page,string $recherche="vide",string $filtre="vide"): Response
    {
        if($recherche == "vide"){
            $recherche = "";
        }
        if($filtre == "vide"){
            $filtre = "";
        }

        $pagination->setEntityClass(Cart::class)
                    ->setSearch($recherche)
                    ->setFiltre($filtre)
                    ->setPage($page)
                    ->setLimit(10);

        $form = $this->createForm(SearchFiltreCartType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $recherche = $form->get('search')->getData();
            $filtre = $form->get('filtre')->getData();
            if($recherche !== null && $filtre !== null){
                $pagination->setSearch($recherche)
                    ->setFiltre($filtre)
                    ->setPage(1);
            }else if($recherche !== null){
                $pagination->setSearch($recherche)
                        ->setFiltre("")
                        ->setPage(1);
            }else if($filtre !== null){
                $pagination->setSearch("")
                        ->setFiltre($filtre)
                        ->setPage(1);
            }else{
                $pagination->setSearch("")
                        ->setFiltre("")
                        ->setPage(1);
            }
        }

        return $this->render('admin/cart/index.html.twig', [
            'pagination' => $pagination,
            'formSearch' => $form->createView(),
        ]);
    }
}
