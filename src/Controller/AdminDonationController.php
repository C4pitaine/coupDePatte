<?php

namespace App\Controller;

use App\Entity\Donation;
use App\Form\SearchFiltrePaiementType;
use App\Service\PaginationFiltreService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminDonationController extends AbstractController
{
    /**
     * Permet de supprimer une donation
     *
     * @param EntityManagerInterface $manager
     * @param Cart $cart
     * @return Response
     */
    #[Route('/admin/donation/{id}/delete',name:'admin_donation_delete')]
    public function delete(EntityManagerInterface $manager,Donation $donation): Response
    {
        $this->addFlash('danger','La donation de '.$donation->getlastName().' '.$donation->getFirstName().' a bien été supprimé');
        $manager->remove($donation);
        $manager->flush();
        return $this->redirectToRoute('admin_donation_index');
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
    #[Route('/admin/donation/{page<\d+>?1}/recherche/{recherche}/filtre/{filtre}', name: 'admin_donation_index')]
    public function index(Request $request,PaginationFiltreService $pagination,int $page,string $recherche="vide",string $filtre="vide"): Response
    {
        if($recherche == "vide"){
            $recherche = "";
        }
        if($filtre == "vide"){
            $filtre = "";
        }

        $pagination->setEntityClass(Donation::class)
                    ->setSearch($recherche)
                    ->setFiltre($filtre)
                    ->setPage($page)
                    ->setLimit(10);

        $form = $this->createForm(SearchFiltrePaiementType::class);
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

        return $this->render('admin/donation/index.html.twig', [
            'pagination' => $pagination,
            'formSearch' => $form->createView(),
        ]);
    }
}
