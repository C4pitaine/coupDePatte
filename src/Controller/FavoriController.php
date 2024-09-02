<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\Favori;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FavoriController extends AbstractController
{
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
}
