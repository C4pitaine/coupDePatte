<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\SearchFiltreArticleType;
use App\Service\PaginationFiltreService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BlogController extends AbstractController
{

     /**
     * Permet d'afficher tout les articles avec une recherche et des filtres ainsi qu'une pagination
     *
     * @param Request $request
     * @param PaginationFiltreService $pagination
     * @param integer $page
     * @param string $recherche
     * @param string $filtre
     * @return Response
     */
    #[Route('/blog/{page<\d+>?1}/recherche/{recherche}/filtre/{filtre}', name: 'blog')]
    public function index(Request $request,PaginationFiltreService $pagination,int $page,string $recherche="vide",string $filtre="vide"): Response
    {
        if($recherche == "vide"){
            $recherche = "";
        }
        if($filtre == "vide"){
            $filtre = "";
        }

        $pagination->setEntityClass(Article::class)
                    ->setSearch($recherche)
                    ->setFiltre($filtre)
                    ->setPage($page)
                    ->setLimit(10)
                    ->setTemplatePath('/partials/_paginationFront.html.twig');

        $form = $this->createForm(SearchFiltreArticleType::class);
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

        return $this->render('blog/index.html.twig', [
            'pagination' => $pagination,
            'formSearch' => $form->createView(),
        ]);
    }
}
