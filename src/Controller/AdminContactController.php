<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\SearchFiltreContactType;
use App\Service\PaginationFiltreService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminContactController extends AbstractController
{
    /**
     * Permet d'afficher un message
     *
     * @param Contact $contact
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/admin/contact/{id}/show',name:'admin_contact_show')]
    public function show(Contact $contact,EntityManagerInterface $manager,Request $request): Response
    {
        $contact->setStatus("vu");
        $manager->persist($contact);
        $manager->flush();
        
        return $this->render('admin/contact/show.html.twig',[
            'contact' => $contact,
        ]);
    }

    /**
     * Permet de supprimer un message
     *
     * @param Contact $contact
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/admin/contact/{id}/delete',name:'admin_contact_delete')]
    public function delete(Contact $contact,EntityManagerInterface $manager): Response
    {
        $this->addFlash('danger','Le message de '.$contact->getLastName().' '.$contact->getFirstName().' a bien été supprimé');

        $manager->remove($contact);
        $manager->flush();
        return $this->redirectToRoute('admin_contact_index');
    }

    /**
     * Permet d'afficher les messages avec une recherche, des filtres et une pagination
     *
     * @param Request $request
     * @param PaginationFiltreService $pagination
     * @param integer $page
     * @param string $recherche
     * @param string $filtre
     * @return Response
     */
    #[Route('/admin/contact/{page<\d+>?1}/recherche/{recherche}/filtre/{filtre}', name: 'admin_contact_index')]
    public function index(Request $request,PaginationFiltreService $pagination,int $page,string $recherche="vide",string $filtre="vide"): Response
    {
        if($recherche == "vide"){
            $recherche = "";
        }
        if($filtre == "vide"){
            $filtre = "";
        }

        $pagination->setEntityClass(Contact::class)
                    ->setSearch($recherche)
                    ->setFiltre($filtre)
                    ->setPage($page)
                    ->setOrder(['id'=>'DESC'])
                    ->setLimit(10);

        $form = $this->createForm(SearchFiltreContactType::class);
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

        return $this->render('admin/contact/index.html.twig', [
            'pagination' => $pagination,
            'formSearch' => $form->createView(),
        ]);
    }
}
