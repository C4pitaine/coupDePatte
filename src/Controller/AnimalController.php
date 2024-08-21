<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Form\SearchFiltreAnimalType;
use App\Service\PaginationTypeService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AnimalController extends AbstractController
{
    /**
     * Permet d'afficher un animal
     *
     * @param Animal $animal
     * @return Response
     */
    #[Route('/animal/{id}/show',name:'animal_show')]
    public function show(Animal $animal): Response
    {
        return $this->render('animal/show.html.twig',[
            'animal' => $animal
        ]);
    }

    /**
     * Permet d'afficher la page des animaux avec une recherche, des filtres et la paginations en fonction dy type de l'animal
     *
     * @param Request $request
     * @param PaginationTypeService $pagination
     * @param integer $page
     * @param string $type
     * @param string $recherche
     * @param string $filtre
     * @return Response
     */
    #[Route('/animal/{type}/page/{page<\d+>?1}/recherche/{recherche}/filtre/{filtre}', name: 'animal_index')]
    public function index(Request $request,PaginationTypeService $pagination,int $page,string $type,string $recherche="vide",string $filtre="vide"): Response
    {
        if($recherche == "vide"){
            $recherche = "";
        }
        if($filtre == "vide"){
            $filtre = "";
        }

        $pagination->setEntityClass(Animal::class)
                    ->setSearch($recherche)
                    ->setFiltre($filtre)
                    ->setPage($page)
                    ->setLimit(10)
                    ->setType($type)
                    ->setTemplatePath('/partials/_paginationTypeFront.html.twig');

        $form = $this->createForm(SearchFiltreAnimalType::class,null,[
            'type' => $type,
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

        return $this->render('animal/index.html.twig', [
            'pagination' => $pagination,
            'formSearch' => $form->createView(),
            'type' => $type
        ]);
    }
}
