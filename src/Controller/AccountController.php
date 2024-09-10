<?php

namespace App\Controller;

use App\Entity\Favori;
use App\Form\SearchFiltreType;
use App\Service\PaginationFiltreService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountController extends AbstractController
{
    #[Route('/account/{page<\d+>?1}/recherche/{recherche}/filtre/{filtre}', name: 'account_index')]
    public function index(Request $request,PaginationFiltreService $pagination,int $page,string $recherche="vide",string $filtre="vide"): Response
    {
        $user = $this->getUser();

        if($recherche == "vide"){
            $recherche = "";
        }
        if($filtre == "vide"){
            $filtre = "";
        }

        $pagination->setEntityClass(Favori::class)
                    ->setSearch($recherche)
                    ->setFiltre($filtre)
                    ->setPage($page)
                    ->setLimit(10)
                    ->setTemplatePath('/partials/_paginationFront.html.twig');

        $form = $this->createForm(SearchFiltreType::class,null,[
            'choices' => [
                "" => "",
                "Chien" => "chien",
                "Chat" => "chat",
                "Lapin" => "lapin",
            ]
        ]);
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

        return $this->render('account/index.html.twig', [
            'pagination' => $pagination,
            'formSearch' => $form->createView(),
            'user' => $user,
        ]);
    }
}
