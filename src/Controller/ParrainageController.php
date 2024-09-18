<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\Parrainage;
use App\Form\ParrainageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ParrainageController extends AbstractController
{
    #[Route('/parrainage', name: 'parrainage')]
    public function index(): Response
    {
        return $this->render('parrainage/index.html.twig', [
            'controller_name' => 'ParrainageController',
        ]);
    }

    #[Route('/parrainage/{id}/create',name:'parrainage_create')]
    #[IsGranted("ROLE_USER")]
    public function create(EntityManagerInterface $manager,Request $request,Animal $animal): Response
    {
        $parrainage = new Parrainage();
        $form = $this->createForm(ParrainageType::class,$parrainage);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $parrainage->addUser($this->getUser())
                        ->addAnimal($animal);

            $manager->persist($parrainage);
            $manager->flush();
        }

        return $this->render('parrainage/create.html.twig',[
            'animal' => $animal,
            'formParrainage' => $form->createView(),
        ]);
    }
}
