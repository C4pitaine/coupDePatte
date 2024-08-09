<?php

namespace App\Controller;

use App\Form\SearchType;
use App\Entity\Indispensable;
use App\Form\IndispensableType;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminIndispensableController extends AbstractController
{

    /**
     * Permet d'ajouter un indispensable
     *
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    #[Route('admin/indispensable/create',name:'admin_indispensable_create')]
    public function create(EntityManagerInterface $manager,Request $request): Response
    {
        $indispensable = new Indispensable();
        $form = $this->createForm(IndispensableType::class,$indispensable);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($indispensable);
            $manager->flush();

            $this->addFlash('success',"L'indispensable : ".$indispensable->getTitle()." a bien été ajouté");
            return $this->redirectToRoute('admin_indispensable_index');
        }

        return $this->render('admin/indispensable/new.html.twig',[
            'myForm' => $form->createView(),
        ]);
    }

    /**
     * Permet de modifier un indispensable
     *
     * @param Indispensable $indispensable
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    #[Route('admin/indispensable/{id}/update',name:'admin_indispensable_update')]
    public function update(Indispensable $indispensable,EntityManagerInterface $manager,Request $request):Response
    {
        $form = $this->createForm(IndispensableType::class,$indispensable);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($indispensable);
            $manager->flush();

            $this->addFlash('warning',"L'indispensable : ".$indispensable->getTitle()." a bien été modifié");
            return $this->redirectToRoute('admin_indispensable_index');
        }

        return $this->render('admin/indispensable/update.html.twig',[
            "myForm" => $form->createView(),
        ]);
    }

    /**
     * Permet de supprimer un indispensable
     *
     * @param EntityManagerInterface $manager
     * @param Indispensable $indispensable
     * @return Response
     */
    #[Route('admin/indispensable/{id}/delete',name:'admin_indispensable_delete')]
    public function delete(EntityManagerInterface $manager,Indispensable $indispensable):Response
    {
        $this->addFlash('danger',"L'indispensable : ".$indispensable->getTitle()." a bien été supprimé");

        $manager->remove($indispensable);
        $manager->flush();

        return $this->redirectToRoute('admin_indispensable_index');
    }

     /**
     * Permet d'afficher les indispensables avec une recherche et une pagination
     *
     * @param Request $request
     * @param PaginationService $pagination
     * @param integer $page
     * @param string $recherche
     * @return Response
     */
    #[Route('/admin/indispensable/{page<\d+>?1}/{recherche}', name: 'admin_indispensable_index')]
    public function index(Request $request,PaginationService $pagination,int $page,string $recherche=""): Response
    {
        $pagination->setEntityClass(Indispensable::class)
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

        return $this->render('admin/indispensable/index.html.twig', [
            'pagination' => $pagination,
            'formSearch' => $form->createView(),
        ]);
    }
}
