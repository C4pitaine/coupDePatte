<?php

namespace App\Controller;

use App\Entity\Donation;
use App\Form\DonationOneType;
use App\Form\DonationTwoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DonationController extends AbstractController
{
    /**
     * Traitement de la première partie pour faire une donation
     *
     * @return Response
     */
    #[Route('/donation/formOne', name: 'donation_one')]
    public function formOne(EntityManagerInterface $manager,Request $request): Response
    {
        $donation = new Donation();
        $form = $this->createForm(DonationOneType::class,$donation);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $donation->setAdresse("")
                    ->setCodePostal(0000)
                    ->setVille("")
                    ->setPays("");
            
            $manager->persist($donation);
            $manager->flush();

            return $this->redirectToRoute('donation_two',['id'=>$donation->getId()]);
        }

        return $this->render('donation/formOne.html.twig', [
            "formOne" => $form->createView(),
        ]);
    }

    /**
     * Traitement de la deuxième parties pour faire une donation
     *
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @param Donation $donation
     * @return Response
     */
    #[Route('/donation/{id}/formTwo',name:"donation_two")]
    public function formTwo(EntityManagerInterface $manager,Request $request,Donation $donation): Response
    {
        $form = $this->createForm(DonationTwoType::class,$donation);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $donation->setStatus("en attente");
            $manager->persist($donation);
            $manager->flush();
        }

        return $this->render('donation/formTwo.html.twig',[
            "formTwo" => $form->createView(),
            'donation' => $donation,
        ]);
    }

    /**
     * Permet de modifier les données du premier formulaire si on revient en arrière depuis le deuxième
     *
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @param Donation $donation
     * @return Response
     */
    #[Route('/donation/{id}/formOne',name:"donation_one_update")]
    public function update(EntityManagerInterface $manager,Request $request,Donation $donation): Response
    {
        $form = $this->createForm(DonationOneType::class,$donation);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($donation);
            $manager->flush();
            return $this->redirectToRoute("donation_two",['id'=>$donation->getId()]);
        }

        return $this->render('donation/formUpdate.html.twig',[
            'formOne' => $form->createView(),
            'donation' => $donation,
        ]);
    }
}
