<?php

namespace App\Controller;

use App\Entity\Donation;
use Dompdf\Dompdf;
use Dompdf\Options;
use Stripe\StripeClient;
use App\Form\DonationOneType;
use App\Form\DonationTwoType;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

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
            $salt = rand(100,100000);
            $token = md5($donation->getEmail().$salt);

            $donation->setAdresse("")
                    ->setCodePostal(0000)
                    ->setVille("")
                    ->setPays("")
                    ->setStatus("en attente")
                    ->setToken($token);
            
            $manager->persist($donation);
            $manager->flush();

            return $this->redirectToRoute('donation_two',['id'=>$donation->getId(),'token'=>$token]);
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
    #[Route('/donation/{id}/formTwo/token/{token}',name:"donation_two")]
    public function formTwo(EntityManagerInterface $manager,Request $request,Donation $donation,string $token): Response
    {
        if($donation){
            if($token == $donation->getToken()){
                $form = $this->createForm(DonationTwoType::class,$donation,[
                    'validation_groups' => ['formTwo']
                ]);
                $form->handleRequest($request);
        
                if($form->isSubmitted() && $form->isValid())
                {
                    $donation->setStatus("en attente");
                    $manager->persist($donation);
                    $manager->flush();
        
                    return $this->redirectToRoute('donation_checkout',['montant'=>$donation->getMontant(),'id'=>$donation->getId(),'token'=>$token]);
                }
            }else{
                throw new BadRequestException('Token invalide');
            }
        }else{
            throw new BadRequestException('Id invalide');
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
    #[Route('/donation/{id}/formOne/token/{token}',name:"donation_one_update")]
    public function update(EntityManagerInterface $manager,Request $request,Donation $donation,string $token): Response
    {
        if($donation){
            if($token == $donation->getToken()){
                $form = $this->createForm(DonationOneType::class,$donation);
                $form->handleRequest($request);

                if($form->isSubmitted() && $form->isValid())
                {
                    $manager->persist($donation);
                    $manager->flush();
                    return $this->redirectToRoute("donation_two",['id'=>$donation->getId(),'token'=>$token]);
                }
            }else{
                throw new BadRequestException('Token invalide');
            }
        }else{
            throw new BadRequestException('Id invalide');
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
    #[Route('/donation/checkout/{montant}/id/{id}/token/{token}',name:'donation_checkout')]
    public function checkout(string $montant,int $id,string $token): Response
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
                'success_url' => "https://coupdepatte.alexandresacre.com/donation/success/".$id."/token/".$token,
                'cancel_url'=>"https://coupdepatte.alexandresacre.com/donation/cancel/".$id
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
    #[Route('/donation/success/{id}/token/{token}',name:'donation_checkout_success')]
    public function checkoutSuccess(EntityManagerInterface $manager,Donation $donation,string $token,MailerInterface $mailer):Response
    {
        if($donation){
            if($token == $donation->getToken()){
                $donation->setStatus('payé');
                $manager->persist($donation);
                $manager->flush();

                $html = $this->renderView('emails/facture.html.twig', [
                    'donateur' => $donation->getLastName()." ".$donation->getFirstName(),
                    'montant' => $donation->getMontant(),
                ]);

                $options = new Options();
                $options->set('defaultFont', 'Arial');
                $dompdf = new Dompdf($options);

                $dompdf->loadHtml($html);
                $dompdf->setPaper('A4', 'portrait');
                $dompdf->render();

                $pdfOutput = $dompdf->output();

                $email = (new TemplatedEmail())
                ->from("noreply@coupdepatte.alexandresacre.com")
                ->to(new Address($donation->getEmail()))
                ->subject('Facture de votre don')
                ->attach($pdfOutput,'facture_de_don.pdf', 'application/pdf')
                ->htmlTemplate('emails/facture.html.twig')
                ->context([
                    'donateur' => $donation->getLastName()." ".$donation->getFirstName(),
                    'montant' => $donation->getMontant(),
                ]);
                $mailer->send($email);
            }else{
                throw new BadRequestException('Token invalide');
            }
        }else{
            throw new BadRequestException('Id invalide');
        }

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
