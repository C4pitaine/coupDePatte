<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\SearchFiltreType;
use Symfony\Component\Mime\Email;
use App\Service\PaginationFiltreService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminContactController extends AbstractController
{
    /**
     * Permet d'afficher un message
     *
     * @param Contact $contact
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/admin/contact/{id}/show',name:'admin_contact_show')]
    public function show(Contact $contact,EntityManagerInterface $manager,Request $request,MailerInterface $mailer): Response
    {
        if($contact->getStatus() == "en attente")
        {
            $contact->setStatus("vu");
            $manager->persist($contact);
            $manager->flush();
        }
        $error = null;

        if($request->isMethod('POST'))
        {
            if($request->request->get('send')){
                $contact->setStatus("repondu");
                $manager->persist($contact);
                $manager->flush();

                $email = (new Email())
                            ->from("noreply@coupdepatte.alexandresacre.com")
                            ->to($contact->getEmail())
                            ->subject("Réponse à votre message de contact")
                            ->text("
                                Bonjour ".$contact->getLastName()." ".$contact->getFirstName()."
                                Voici notre réponse à votre message :
                                ".$request->request->get('send')."
                                Bien à vous,
                                L\'équipe Coup de patte.
                            ")
                            ->html('
                                <h2>Bonjour '.$contact->getLastName().' '.$contact->getFirstName().'</h2>
                                <p>Voici notre réponse à votre message :</p>
                                <p>'.$request->request->get('send').'</p>
                                <p>Bien à vous,</p>
                                <p>L\'équipe Coup de patte.</p>
                            ');
                $mailer->send($email);
                
                $this->addFlash('success','Votre réponse a bien été envoyée');
                return $this->redirectToRoute('admin_contact_index');
            }else{
                $error = 'Votre réponse est vide';
            }
        }
        
        return $this->render('admin/contact/show.html.twig',[
            'contact' => $contact,
            'error' => $error,
        ]);
    }

    /**
     * Permet de supprimer un message
     *
     * @param Contact $contact
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/admin/contact/{id}/delete',name:'admin_contact_delete')]
    public function delete(Contact $contact,EntityManagerInterface $manager): Response
    {
        $this->addFlash('danger','Le message de '.$contact->getLastName().' '.$contact->getFirstName().' a bien été supprimé');

        $manager->remove($contact);
        $manager->flush();
        return $this->redirectToRoute('admin_contact_index');
    }

    /**
     * Permet d'afficher les messages avec une recherche, des filtres et une pagination
     *
     * @param Request $request
     * @param PaginationFiltreService $pagination
     * @param integer $page
     * @param string $recherche
     * @param string $filtre
     * @return Response
     */
    #[Route('/admin/contact/{page<\d+>?1}/recherche/{recherche}/filtre/{filtre}', name: 'admin_contact_index')]
    public function index(Request $request,PaginationFiltreService $pagination,int $page,string $recherche="vide",string $filtre="vide"): Response
    {
        if($recherche == "vide"){
            $recherche = "";
        }
        if($filtre == "vide"){
            $filtre = "";
        }

        $pagination->setEntityClass(Contact::class)
                    ->setSearch($recherche)
                    ->setFiltre($filtre)
                    ->setPage($page)
                    ->setOrder(['id'=>'DESC'])
                    ->setLimit(10);

        $form = $this->createForm(SearchFiltreType::class,null,[
            'choices' => [
                "" => "",
                "Message en attente" => 'en attente',
                "Message vu" => 'vu',
                "Message Répondu" => 'repondu'
            ]
        ]);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $recherche = $form->get('search')->getData();
            $filtre = $form->get('filtre')->getData();
            if($recherche !== null && $filtre !== null){
                $pagination->setSearch($recherche)
                    ->setFiltre($filtre)
                    ->setPage(1);
            }else if($recherche !== null){
                $pagination->setSearch($recherche)
                        ->setFiltre("")
                        ->setPage(1);
            }else if($filtre !== null){
                $pagination->setSearch("")
                        ->setFiltre($filtre)
                        ->setPage(1);
            }else{
                $pagination->setSearch("")
                        ->setFiltre("")
                        ->setPage(1);
            }
        }

        return $this->render('admin/contact/index.html.twig', [
            'pagination' => $pagination,
            'formSearch' => $form->createView(),
        ]);
    }
}
