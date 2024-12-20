<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\AnimalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(EntityManagerInterface $manager,Request $request,AnimalRepository $repo): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class,$contact);
        $form->handleRequest($request);

        $numberPensionnaire = Count($repo->findBy(['adopted'=>false]));
        if($numberPensionnaire < 8){
            $lastPensionnaires = $repo->findBy(['adopted'=>false],null,8);
        }else{
            $lastPensionnaires = $repo->findBy(['adopted'=>false],null,8,$numberPensionnaire-8);
        }

        $contact->setStatus("en attente");

        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($contact);
            $manager->flush();

            $this->addFlash('success','Votre message a bien été envoyé');
            return new RedirectResponse($this->generateUrl('home').'#myContact');
        }
        return $this->render('home.html.twig', [
            'formContact' => $form->createView(),
            'lastPensionnaires' => $lastPensionnaires,
        ]);
    }

    /**
     * Permet d'afficher la page des mentions légales
     *
     * @return Response
     */
    #[Route('/legals',name:"legals")]
    public function legals(): Response
    {
        return $this->render('legals/index.html.twig');
    }

    /**
     * Permet d'afficher la page avec les informations pour pouvoir adopter
     *
     * @return Response
     */
    #[Route('/legals/adoption',name:'legals_adoption')]
    public function adoption(): Response
    {
        return $this->render('legals/adoption.html.twig');
    }

    /**
     * Permet d'afficher la page avec les informations pour pouvoir adopter
     *
     * @return Response
     */
    #[Route('/legals/sitemap',name:'legals_sitemap')]
    public function siteMap(): Response
    {
        return $this->render('legals/sitemap.html.twig');
    }
}
