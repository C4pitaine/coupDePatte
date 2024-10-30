<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\Favori;
use App\Repository\FavoriRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FavoriController extends AbstractController
{
    /**
     * Permet d'ajouter un favori
     *
     * @param Animal $animal
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/favori/{id}/create', name: 'favori_create')]
    #[IsGranted('ROLE_USER')]
    public function add(Animal $animal,EntityManagerInterface $manager): Response
    {
        $favori = new Favori();

        $favori->addUser($this->getUser())
                ->addAnimal($animal);
        
        $manager->persist($favori);
        $manager->flush();

        return $this->redirectToRoute('animal_show',['id'=>$animal->getId()]);
    }

    /**
     * Permet de supprimer un favori
     *
     * @param Favori $favori
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/favori/{id}/delete', name: 'favori_delete')]
    #[IsGranted('ROLE_USER')]
    public function remove(Animal $animal,EntityManagerInterface $manager,FavoriRepository $repo):Response
    {       
        $user = $this->getUser();
        $favori = $repo->findOneFavori($user->getId(),$animal->getId());

        if($favori){
            $manager->remove($favori);
            $manager->flush();

            return $this->redirectToRoute('account_index');
        }else{
            throw new NotFoundHttpException("Erreur 404");
        }
    }
}
