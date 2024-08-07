<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Form\SearchType;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAnimauxController extends AbstractController
{
    /**
     * Permet d'ajouter un animal 
     *
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    #[Route('admin/animal/new', name: 'admin_animal_new')]
    public function create(EntityManagerInterface $manager,Request $request): Response
    {
        $animal = new Animal();

        // $form = $this->createForm();

        return $this->render('admin/animal/new.html.twig',[

        ]);
    }

    #[Route('/admin/animal/{page<\d+>?1}/{recherche}', name: 'admin_animal_index')]
    public function index(Request $request,PaginationService $pagination,int $page,string $recherche=""): Response
    {
        $pagination->setEntityClass(Animal::class)
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

        return $this->render('admin/animal/index.html.twig', [
            'pagination' => $pagination,
            'formSearch' => $form->createView(),
        ]);
    }
}