<?php

namespace App\Controller;

use App\Entity\Parrainage;
use App\Form\SearchFiltreType;
use App\Service\PaginationFiltreService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminParrainageController extends AbstractController
{
    #[Route('/admin/parrainage/{id}/delete',name:"admin_parrainage_delete")]
    public function delete(Parrainage $parrainage,EntityManagerInterface $manager): Response
    {
        foreach($parrainage->getAnimal() as $animal){
            foreach($parrainage->getUser() as $user){
                $this->addFlash('danger','Le parrainage de '.$animal->getName().'  par '.$user->getLastName().' '.$user->getFirstName().'a bien été supprimé');
                $manager->remove($parrainage);
                $manager->flush();
                return $this->redirectToRoute('admin_parrainage_index');
            }
        }
       
    }

     /**
     * Permet d'afficher les parrainage avec une recherche et des filtres avec les résultats paginés
     *
     * @param Request $request
     * @param PaginationFiltreService $pagination
     * @param integer $page
     * @param string $recherche
     * @param string $filtre
     * @return Response
     */
    #[Route('/admin/parrainage/{page<\d+>?1}/recherche/{recherche}/filtre/{filtre}', name: 'admin_parrainage_index')]
    public function index(Request $request,PaginationFiltreService $pagination,int $page,string $recherche="vide",string $filtre="vide"): Response
    {
        if($recherche == "vide"){
            $recherche = "";
        }
        if($filtre == "vide"){
            $filtre = "";
        }

        $pagination->setEntityClass(Parrainage::class)
                    ->setSearch($recherche)
                    ->setFiltre($filtre)
                    ->setPage($page)
                    ->setLimit(10);

        $form = $this->createForm(SearchFiltreType::class,null,[
            'choices' => [
                "" => "",
                "Payé" => "payé",
                "Stoppé" => "stoppé car adopté",
                "Annulé" => "annulé",
                "En attente" => "en attente",
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

        return $this->render('admin/parrainage/index.html.twig', [
            'pagination' => $pagination,
            'formSearch' => $form->createView(),
        ]);
    }
}
