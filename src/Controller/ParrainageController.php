<?php

namespace App\Controller;

use App\Entity\Animal;
use Stripe\StripeClient;
use App\Entity\Parrainage;
use App\Form\ParrainageType;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ParrainageController extends AbstractController
{
    #[Route('/parrainage', name: 'parrainage')]
    public function index(): Response
    {
        return $this->render('parrainage/index.html.twig', [
            'controller_name' => 'ParrainageController',
        ]);
    }

    /**
     * Creation d'un parrainage
     *
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @param Animal $animal
     * @return Response
     */
    #[Route('/parrainage/{id}/create',name:'parrainage_create')]
    #[IsGranted("ROLE_USER")]
    public function create(EntityManagerInterface $manager,Request $request,Animal $animal): Response
    {
        $parrainage = new Parrainage();
        $user = $this->getUser();
        $form = $this->createForm(ParrainageType::class,$parrainage);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {

            $salt = rand(100,100000);
            $token = md5($user->getEmail().$salt);
            $parrainage->addUser($user)
                        ->addAnimal($animal)
                        ->setToken($token)
                        ->setStatus("en attente");

            $manager->persist($parrainage);
            $manager->flush();

            return $this->redirectToRoute('parrainage_checkout',['montant'=>$parrainage->getMontant(),'id'=>$parrainage->getId(),'token'=>$token]);
        }

        return $this->render('parrainage/create.html.twig',[
            'animal' => $animal,
            'formParrainage' => $form->createView(),
        ]);
    }

    /**
     * Transaction Stripe
     *
     * @param string $total
     * @param integer $id
     * @return Response
     */
    #[Route('/parrainage/checkout/{montant}/id/{id}/token/{token}',name:'parrainage_checkout')]
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
                'success_url' => "http://127.0.0.1:8000/parrainage/success/".$id."/token/".$token,
                'cancel_url'=>"http://127.0.0.1:8000/parrainage/cancel/".$id
            ]);

            return $this->redirect($checkout->url);

        return $this->render('parrainage/checkout.html.twig');
    }

    /**
     * Page de réussite de transaction
     *
     * @param Request $request
     * @param Cart $cart
     * @return Response
     */
    #[Route('/parrainage/success/{id}/token/{token}',name:'parrainage_checkout_success')]
    public function checkoutSuccess(EntityManagerInterface $manager,Parrainage $parrainage,string $token,MailerInterface $mailer):Response
    {
        if($parrainage){
            $user = $this->getUser();
            if($token == $parrainage->getToken()){
                $parrainage->setStatus('payé');
                $manager->persist($parrainage);
                $manager->flush();

                $email = (new TemplatedEmail())
                ->from("noreply@coupdepatte.alexandresacre.com")
                ->to(new Address($user->getEmail()))
                ->subject('Facture de votre don')
                ->htmlTemplate('emails/facture.html.twig')
                ->context([
                    'donateur' => $user->getLastName()." ".$user->getFirstName(),
                    'montant' => $parrainage->getMontant(),
                ]);
                $mailer->send($email);
            }else{
                throw new BadRequestException('Token invalide');
            }
        }else{
            throw new BadRequestException('Id invalide');
        }

        return $this->render('parrainage/success.html.twig',[
            'parrainage' => $parrainage,
        ]);
    }   

    /**
     * Renvoit à la page d'annulation de la transaction
     *
     * @return Response
     */
    #[Route('/parrainage/cancel/{id}',name:'parrainage_checkout_cancel')]
    public function checkoutCancel(EntityManagerInterface $manager,Parrainage $parrainage):Response
    {
        $parrainage->setStatus('annulé');
        $manager->persist($parrainage);
        $manager->flush();

        return $this->render('parrainage/cancel.html.twig',[
            'parrainage' => $parrainage,
        ]);
    }  
}
