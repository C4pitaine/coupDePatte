<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Form\SearchFiltreAnimalType;
use App\Repository\FavoriRepository;
use App\Repository\ParrainageRepository;
use App\Service\PaginationTypeService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AnimalController extends AbstractController
{
    /**
     * Permet d'afficher un animal et de vérifier si l'animal est dans les favoris du user
     *
     * @param Animal $animal
     * @return Response
     */
    #[Route('/animal/{id}/show',name:'animal_show')]
    public function show(Animal $animal,FavoriRepository $repo,ParrainageRepository $parrainageRepo): Response
    {
        $user = $this->getUser();
        $isFavori = false;
        $isParrain = false;
        
        if($user){
            $favoris = $repo->getFavoriFromUser($user->getId());
            foreach($favoris as $favori)
            {
                $animaux = $favori->getAnimal();

                foreach($animaux as $item){
                    if($item->getName() == $animal->getName()){
                        $isFavori = true;
                    }
                }
            }

            $parrainages = $parrainageRepo->getParrainageFromUser($user->getId());
            foreach($parrainages as $parrainage)
            {
                $animaux = $parrainage->getAnimal();
                foreach($animaux as $itemParrainage){
                    if($itemParrainage->getName() == $animal->getName())
                    {
                        $isParrain = true;
                    }
                }
            }
        }
        return $this->render('animal/show.html.twig', [
            'animal' => $animal,
            'isFavori' => $isFavori,
            'isParrain' => $isParrain,
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

        /* Vérifie que le type correspond bien à celui attendu */
        if($type != "chat" && $type != "chien" && $type != "lapin"){
            throw new NotFoundHttpException('Erreur 404');
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
