<?php

namespace App\Controller;

use App\Entity\Friandise;
use App\Form\FriandiseType;
use App\Form\SearchFiltreType;
use App\Form\FriandiseModifyType;
use App\Service\PaginationFiltreService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AdminFriandiseController extends AbstractController
{

    /**
     * Permet d'ajouter une friandise
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/admin/friandise/create',name:'admin_friandise_create')]
    public function create(Request $request,EntityManagerInterface $manager): Response
    {
        $friandise = new Friandise();
        
        $form = $this->createForm(FriandiseType::class,$friandise);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $file = $form['image']->getData();
            if(!empty($file))
            {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename."-".uniqid().'.'.$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('uploads_directory_friandise'), 
                        $newFilename 
                    );
                }catch(FileException $e)
                {
                    return $e->getMessage();
                }
                $friandise->setImage($newFilename);
            }
            $manager->persist($friandise);
            $manager->flush();

            $this->addFlash('success','Votre friandise a bien été ajoutée');
            return $this->redirectToRoute('admin_friandise_index');
        }

        return $this->render('admin/friandise/new.html.twig',[
            "myForm" => $form->createView(),
        ]);
    }

    /**
     * Permet de supprimer une friandise
     *
     * @param EntityManagerInterface $manager
     * @param Friandise $friandise
     * @return Response
     */
    #[Route('/admin/friandise/{id}/delete',name:"admin_friandise_delete")]
    public function delete(EntityManagerInterface $manager,Friandise $friandise):Response
    {
        $this->addFlash('danger','La friandise '.$friandise->getName().' a bien été supprimée');

        if(!empty($friandise->getImage()))
        {
            unlink($this->getParameter('uploads_directory_friandise').'/'.$friandise->getImage());
            $friandise->setImage('');
            $manager->persist($friandise);
        }

        $manager->remove($friandise);
        $manager->flush();

        return $this->redirectToRoute('admin_friandise_index');
    }

    /**
     * Permet de modifier une friandise
     *
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @param Friandise $friandise
     * @return Response
     */
    #[Route('/admin/friandise/{id}/update',name:'admin_friandise_update')]
    public function update(EntityManagerInterface $manager,Request $request,Friandise $friandise): Response
    {
        $friandiseImage = $friandise->getImage();
        $friandise->setImage("");
        $form = $this->createForm(FriandiseModifyType::class,$friandise);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $friandise->setImage($friandiseImage);
            $file = $form['image']->getData();
            if(!empty($file))
            {
                if(!empty($friandiseImage)){
                    unlink($this->getParameter('uploads_directory_friandise').'/'.$friandiseImage);
                }
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename."-".uniqid().'.'.$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('uploads_directory_friandise'), 
                        $newFilename 
                    );
                }catch(FileException $e)
                {
                    return $e->getMessage();
                }
                $friandise->setImage($newFilename);
            }else{
                if(!empty($friandiseImage)){
                    $friandise->setImage($friandiseImage);
                }
            }

            $manager->persist($friandise);
            $manager->flush();

            $this->addFlash('warning','La friandise '.$friandise->getName().' a bien été modifiée');
            return $this->redirectToRoute('admin_friandise_index');
        }

        return $this->render('admin/friandise/update.html.twig',[
            'myForm' => $form->createView(),
            'friandise' => $friandise,
            'friandiseImage' => $friandiseImage,
        ]);
    }
    
    /**
     * Permet d'afficher les friandises avec une recherche et une pagination
     *
     * @param Request $request
     * @param PaginationFiltreService $pagination
     * @param integer $page
     * @param string $recherche
     * @param string $filtre
     * @return Response
     */
    #[Route('/admin/friandise/{page<\d+>?1}/recherche/{recherche}/filtre/{filtre}', name: 'admin_friandise_index')]
    public function index(Request $request,PaginationFiltreService $pagination,int $page,string $recherche="vide",string $filtre="vide"): Response
    {
        if($recherche == "vide"){
            $recherche = "";
        }
        if($filtre == "vide"){
            $filtre = "";
        }

        $pagination->setEntityClass(Friandise::class)
                    ->setSearch($recherche)
                    ->setFiltre($filtre)
                    ->setPage($page)
                    ->setLimit(10);

        $form = $this->createForm(SearchFiltreType::class,null,[
            'choices' => [
                "" => "",
                "Chat" => "chat",
                "Chien" => "chien",
                "Lapin" => "lapin"
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

        return $this->render('admin/friandise/index.html.twig', [
            'pagination' => $pagination,
            'formSearch' => $form->createView(),
        ]);
    }
}
