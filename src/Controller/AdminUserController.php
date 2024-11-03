<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AddFamilleAccueilType;
use App\Form\SearchType;
use App\Repository\FavoriRepository;
use App\Repository\ParrainageRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUserController extends AbstractController
{
    /**
     * Permet de supprimer un user
     *
     * @param EntityManagerInterface $manager
     * @param User $user
     * @return Response
     */
    #[Route('admin/user/{id}/delete',name:'admin_user_delete')]
    public function delete(EntityManagerInterface $manager,User $user,ParrainageRepository $parrainageRepo,FavoriRepository $favoriRepository):Response
    {
        $this->addFlash('danger',"L'user : ".$user->getLastName()." ".$user->getFirstName()." a bien été supprimé");

        $parrainages = $parrainageRepo->getAllParrainageFromUser($user->getId());
        foreach($parrainages as $parrainage){
            $manager->remove($parrainage);
        }

        $favoris = $favoriRepository->getFavoriFromUser($user->getId());
        foreach($favoris as $favori){
            $manager->remove($favori);
        }

        $manager->remove($user);
        $manager->flush();

        return $this->redirectToRoute('admin_user_index');
    }

    /**
     * Permet d'ajouter un utilisateur comme famille d'accueil
     *
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @param User $user
     * @return Response
     */
    #[Route('admin/user/{id}/update',name:'admin_user_update')]
    public function update(EntityManagerInterface $manager,Request $request,User $user): Response
    {
        $form = $this->createForm(AddFamilleAccueilType::class,$user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {   
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success',"La modification pour ".$user->getLastName()." ".$user->getFirstName()." a bien été effectuée");
            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin/user/update.html.twig',[
            'myForm' => $form->createView(),
        ]);
    }

     /**
     * Permet d'afficher les users avec une recherche et une pagination
     *
     * @param Request $request
     * @param PaginationService $pagination
     * @param integer $page
     * @param string $recherche
     * @return Response
     */
    #[Route('/admin/user/{page<\d+>?1}/{recherche}', name: 'admin_user_index')]
    public function index(Request $request,PaginationService $pagination,int $page,string $recherche=""): Response
    {
        $pagination->setEntityClass(User::class)
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

        return $this->render('admin/user/index.html.twig', [
            'pagination' => $pagination,
            'formSearch' => $form->createView(),
        ]);
    }
}
