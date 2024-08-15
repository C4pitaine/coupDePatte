<?php

namespace App\Controller;

use App\Entity\Adoption;
use App\Form\AdoptionOneType;
use App\Form\AdoptionTwoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdoptionController extends AbstractController
{
    /**
     * Traitement de la première partie pour faire adopter son animal
     *
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    #[Route('/adoption/formOne', name: 'adoption_one')]
    public function formOne(EntityManagerInterface $manager,Request $request): Response
    {
        $adoption = new Adoption();

        $form = $this->createForm(AdoptionOneType::class,$adoption);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $adoption->setImage("");
            $adoption->setDescription("");

            $manager->persist($adoption);
            $manager->flush();
            return $this->redirectToRoute('adoption_two',['id'=>$adoption->getId()]);
        }
        

        return $this->render('adoption/formOne.html.twig', [
            'formOne' => $form->createView(),
        ]);
    }

    /**
     * Traitement de la deuxième partie pour faire adopter son animal
     *
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @param Adoption $adoption
     * @return Response
     */
    #[Route('/adoption/{id}/formTwo',name:"adoption_two")]
    public function formTwo(EntityManagerInterface $manager,Request $request,Adoption $adoption): Response
    {
        $form = $this->createForm(AdoptionTwoType::class,$adoption);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($adoption);
            $manager->flush();

            $this->addFlash('success',"Le formulaire d'adoption a bien été envoyé" );
            return $this->redirectToRoute('adoption_show',['id'=>$adoption->getId()]);
        }

        return $this->render('adoption/formTwo.html.twig',[
            'formTwo' => $form->createView(),
        ]);
    }

    /**
     * Permet de modifier les données du premier formulaire si on revient en arrière depuis le deuxième
     *
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @param Adoption $adoption
     * @return Response
     */
    #[Route('/adoption/{id}/formOne',name:'adoption_one_update')]
    public function update(EntityManagerInterface $manager,Request $request,Adoption $adoption): Response
    {
        $form = $this->createForm(AdoptionOneType::class,$adoption);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($adoption);
            $manager->flush();

            return $this->redirectToRoute('adoption_two',['id'=>$adoption->getId()]);
        }

        return $this->render('adoption/formTwo.html.twig',[
            'formOne' => $form->createView(),
        ]);
    }

    /**
     * Permet d'afficher les infos après avoir remplit le formulaire d'adoption
     *
     * @param Adoption $adoption
     * @return Response
     */
    #[Route('/adoption/{id}/show')]
    public function show(Adoption $adoption): Response
    {
        return $this->render('adoption/show.html.twig',[
            'adoption' => $adoption,
        ]);
    }
}
