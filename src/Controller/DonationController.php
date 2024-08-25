<?php

namespace App\Controller;

use App\Entity\Donation;
use Stripe\StripeClient;
use App\Form\DonationOneType;
use App\Form\DonationTwoType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
                    ->setPays("")
                    ->setStatus("en attente");
            
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
        $form = $this->createForm(DonationTwoType::class,$donation,[
            'validation_groups' => ['formTwo']
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $donation->setStatus("en attente");
            $manager->persist($donation);
            $manager->flush();

            return $this->redirectToRoute('donation_checkout',['montant'=>$donation->getMontant(),'id'=>$donation->getId()]);
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

    /**
     * Transaction Stripe
     *
     * @param string $total
     * @param integer $id
     * @return Response
     */
    #[Route('/donation/checkout/{montant}/id/{id}',name:'donation_checkout')]
    public function checkout(string $montant,int $id): Response
    {
        $gateway = new StripeClient($_ENV['STRIPE_SECRETKEY']);
        $amount = intval($montant)*100;

        $checkout = $gateway->checkout->sessions->create(
            [
                'line_items'=>[[
                    'price_data'=>[
                        'currency'=>$_ENV['STRIPE_CURRENCY'],
                        'product_data'=>[
                            'name'=>'Donations',
                        ],
                        'unit_amount'=>$amount,
                    ],
                    'quantity'=>'1'
                ]],
                'mode'=>'payment',
                'success_url' => "http://127.0.0.1:8000/donation/success/".$id,
                'cancel_url'=>"http://127.0.0.1:8000/donation/cancel/".$id
            ]);

            return $this->redirect($checkout->url);

        return $this->render('donation/checkout.html.twig');
    }

    /**
     * Page de réussite de transaction
     *
     * @param Request $request
     * @param Cart $cart
     * @return Response
     */
    #[Route('/donation/success/{id}',name:'donation_checkout_success')]
    public function checkoutSuccess(EntityManagerInterface $manager,Donation $donation):Response
    {
        $donation->setStatus('payé');
        $manager->persist($donation);
        $manager->flush();

        return $this->render('donation/success.html.twig',[
            'donation' => $donation,
        ]);
    }   

    /**
     * Renvoit à la page d'annulation de la transaction
     *
     * @return Response
     */
    #[Route('/donation/cancel/{id}',name:'donation_checkout_cancel')]
    public function checkoutCancel(EntityManagerInterface $manager,Donation $donation):Response
    {
        $donation->setStatus('annulé');
        $manager->persist($donation);
        $manager->flush();

        return $this->render('donation/cancel.html.twig',[
            'donation' => $donation,
        ]);
    }   
}
