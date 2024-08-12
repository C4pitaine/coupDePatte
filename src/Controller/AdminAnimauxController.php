<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Form\AnimalModifyType;
use App\Form\AnimalType;
use App\Form\SearchType;
use App\Form\SearchFiltreType;
use App\Service\PaginationService;
use App\Service\PaginationFiltreService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AdminAnimauxController extends AbstractController
{
    /**
     * Permet d'ajouter un animal 
     *
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    #[Route('admin/animal/create', name: 'admin_animal_create')]
    public function create(EntityManagerInterface $manager,Request $request): Response
    {
        $animal = new Animal();

        $form = $this->createForm(AnimalType::class,$animal);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $file = $form['coverImage']->getData();
            if(!empty($file))
            {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename."-".uniqid().'.'.$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('uploads_directory_animal'), 
                        $newFilename 
                    );
                }catch(FileException $e)
                {
                    return $e->getMessage();
                }
                $animal->setCoverImage($newFilename);
            }
            $manager->persist($animal);
            $manager->flush();

            $this->addFlash("success","L'animal a bien été ajouté");
            return $this->redirectToRoute("admin_animal_index");
        }

        return $this->render('admin/animal/new.html.twig',[
            "myForm" => $form->createView(),
        ]);
    }

    /**
     * Permet de modifier les données d'un animal
     *
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @param Animal $animal
     * @return Response
     */
    #[Route('/admin/animal/{id}/update',name:"admin_animal_update")]
    public function update(EntityManagerInterface $manager,Request $request,Animal $animal):Response
    {
        $animalImage = $animal->getCoverImage();
        $animal->setCoverImage("");
        $form = $this->createForm(AnimalModifyType::class,$animal);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $animal->setCoverImage($animalImage);

            $file = $form['coverImage']->getData();
            if(!empty($file))
            {
                if(!empty($animalImage)){
                    unlink($this->getParameter('uploads_directory_animal').'/'.$animalImage);
                }
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename."-".uniqid().'.'.$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('uploads_directory_animal'), 
                        $newFilename 
                    );
                }catch(FileException $e)
                {
                    return $e->getMessage();
                }
                $animal->setCoverImage($newFilename);
            }else{
                if(!empty($animalImage)){
                    $animal->setCoverImage($animalImage);
                }
            }

            $manager->persist($animal);
            $manager->flush();

            $this->addFlash('warning','Le profil de '.$animal->getName().' a bien été modifié');
            return $this->redirectToRoute('admin_animal_index');
        }

        return $this->render('admin/animal/update.html.twig',[
            'myForm' => $form->createView(),
            'animal' => $animal,
            'animalImage' => $animalImage,
        ]);
    }

    #[Route('/admin/animal/{id}/delete',name:"admin_animal_delete")]
    public function delete(EntityManagerInterface $manager,Animal $animal):Response
    {
        $this->addFlash('danger','Le profil de '.$animal->getName().' a bien été supprimé');

        if(!empty($animal->getCoverImage()))
        {
            unlink($this->getParameter('uploads_directory_animal').'/'.$animal->getCoverImage());
            $animal->setCoverImage('');
            $manager->persist($animal);
        }

        $manager->remove($animal);
        $manager->flush();

        return $this->redirectToRoute('admin_animal_index');
    }
    
    /**
     * Permet d'afficher les animaux avec une recherche / filtre et une pagination
     *
     * @param Request $request
     * @param PaginationFiltreService $pagination
     * @param integer $page
     * @param string $recherche
     * @param string $filtre
     * @return Response
     */
    #[Route('/admin/animal/{page<\d+>?1}/recherche/{recherche}/filtre/{filtre}', name: 'admin_animal_index')]
    public function index(Request $request,PaginationFiltreService $pagination,int $page,string $recherche="vide",string $filtre="vide"): Response
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
                    ->setLimit(10);

        $form = $this->createForm(SearchFiltreType::class);
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

        return $this->render('admin/animal/index.html.twig', [
            'pagination' => $pagination,
            'formSearch' => $form->createView(),
            "search" => $recherche,
            "filtre" => $filtre
        ]);
    }
}