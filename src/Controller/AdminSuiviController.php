<?php

namespace App\Controller;

use App\Entity\Suivi;
use App\Form\SearchType;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminSuiviController extends AbstractController
{
    /**
     * Permet d'afficher un suivi
     *
     * @param Suivi $suivi
     * @return Response
     */
    #[Route('/admin/suivi/{id}/show',name:'admin_suivi_show')]
    public function show(Suivi $suivi): Response
    {
        return $this->render('admin/suivi/show.html.twig',[
            'suivi' => $suivi
        ]);
    }

    /**
     * Permet de supprimer un suivi
     *
     * @param Contact $contact
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/admin/suivi/{id}/delete',name:'admin_suivi_delete')]
    public function delete(Suivi $suivi,EntityManagerInterface $manager): Response
    {
        $this->addFlash('danger','Le suivi de '.$suivi->getAnimal().' a bien été supprimé');

        $manager->remove($suivi);
        $manager->flush();
        return $this->redirectToRoute('admin_suivi_index');
    }

    /**
     * Permet d'afficher les suivis avec une recherche et une pagination
     *
     * @param Request $request
     * @param PaginationService $pagination
     * @param integer $page
     * @param string $recherche
     * @return Response
     */
    #[Route('/admin/suivi/{page<\d+>?1}/{recherche}', name: 'admin_suivi_index')]
    public function index(Request $request,PaginationService $pagination,int $page,string $recherche=""): Response
    {
        $pagination->setEntityClass(Suivi::class)
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

        return $this->render('admin/suivi/index.html.twig', [
            'pagination' => $pagination,
            'formSearch' => $form->createView(),
        ]);
    }
}
