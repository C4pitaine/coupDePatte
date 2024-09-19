<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\Favori;
use App\Repository\FavoriRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
    public function add(Animal $animal,EntityManagerInterface $manager): Response
    {
        $favori = new Favori();

        $favori->addUser($this->getUser())
                ->addAnimal($animal);
        
        $manager->persist($favori);
        $manager->flush();

        $this->addFlash('success',$animal->getName().' a bien été ajouté à vos favoris');
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
    public function remove(Favori $favori,EntityManagerInterface $manager):Response
    {
        foreach($favori->getAnimal() as $favoriAnimal){
            $this->addFlash('success',$favoriAnimal->getName().' a bien été retiré des favoris');

            $manager->remove($favori);
            $manager->flush();
            return $this->redirectToRoute('animal_show',['id'=>$favoriAnimal->getId()]);
        }
    }
}
