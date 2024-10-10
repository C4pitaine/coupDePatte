<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Form\AnimalType;
use App\Form\AnimalModifyType;
use App\Form\SearchFiltreType;
use Symfony\Component\Mime\Email;
use App\Repository\ParrainageRepository;
use App\Service\PaginationFiltreService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AdminAnimauxController extends AbstractController
{
    /**
     * Permet d'ajouter un animal 
     *
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    #[Route('admin/animal/create', name: 'admin_animal_create')]
    public function create(EntityManagerInterface $manager,Request $request): Response
    {
        $animal = new Animal();

        $form = $this->createForm(AnimalType::class,$animal);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $file = $form['coverImage']->getData();
            if(!empty($file))
            {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename."-".uniqid().'.'.$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('uploads_directory_animal'), 
                        $newFilename 
                    );
                }catch(FileException $e)
                {
                    return $e->getMessage();
                }
                $animal->setCoverImage($newFilename);
            }
            $manager->persist($animal);
            $manager->flush();

            $this->addFlash("success","L'animal a bien été ajouté");
            return $this->redirectToRoute("admin_animal_index");
        }

        return $this->render('admin/animal/new.html.twig',[
            "myForm" => $form->createView(),
        ]);
    }

    /**
     * Permet de modifier les données d'un animal
     *
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @param Animal $animal
     * @return Response
     */
    #[Route('/admin/animal/{id}/update',name:"admin_animal_update")]
    public function update(EntityManagerInterface $manager,Request $request,Animal $animal,ParrainageRepository $repo,MailerInterface $mailer):Response
    {
        $animalImage = $animal->getCoverImage();
        $animal->setCoverImage("");
        $form = $this->createForm(AnimalModifyType::class,$animal);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $animal->setCoverImage($animalImage);

            $file = $form['coverImage']->getData();
            if(!empty($file))
            {
                if(!empty($animalImage)){
                    unlink($this->getParameter('uploads_directory_animal').'/'.$animalImage);
                }
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename."-".uniqid().'.'.$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('uploads_directory_animal'), 
                        $newFilename 
                    );
                }catch(FileException $e)
                {
                    return $e->getMessage();
                }
                $animal->setCoverImage($newFilename);
            }else{
                if(!empty($animalImage)){
                    $animal->setCoverImage($animalImage);
                }
            }

            $adopted = $form['adopted']->getData();
            if($adopted){
                $parrainages = $repo->getParrainageFromAnimal($animal->getId());
                foreach($parrainages as $parrainage){
                    $parrainage->setMontant(0)
                                ->setStatus("stoppé car adopté");
                    $users = $parrainage->getUser();
                    foreach($users as $user){
                        $email = (new Email())
                                ->from("noreply@coupdepatte.alexandresacre.com")
                                ->to($user->getEmail())
                                ->subject("Parrainage de ".$animal->getName())
                                ->text("
                                    Coup de patte - Refuge animalier
                                    Bonjour ".$user->getLastName()." ".$user->getFirstName()."
                                    Nous vous envoyons ce courriel pour vous informer que ".$animal->getName()."sssss a été adopté par une famille.
                                    Le montant de votre parrainage est donc réduit à 0 €, et vous ne serez plus débité pour ce parrainage.
                                    L’équipe Coup de Patte
                                    Bien à vous,
                                ")
                                ->html('
                                    <h1>Coup de patte - Refuge animalier</h1>
                                    <p>Bonjour '.$user->getLastName().' '.$user->getFirstName().'</p>
                                    <p>Nous vous envoyons ce courriel pour vous informer que '.$animal->getName().' a été adopté par une famille.</p>
                                    <p>Le montant de votre parrainage est donc réduit à 0 €, et vous ne serez plus débité pour ce parrainage.</p>
                                    <p>Bien à vous,</p>
                                    <p>L\'équipe Coup de Patte</p>
                                ');
                         $mailer->send($email);
                    }
                    $manager->persist($parrainage);
                }
            }

            $manager->persist($animal);
            $manager->flush();

            $this->addFlash('warning','Le profil de '.$animal->getName().' a bien été modifié');
            return $this->redirectToRoute('admin_animal_index');
        }

        return $this->render('admin/animal/update.html.twig',[
            'myForm' => $form->createView(),
            'animal' => $animal,
            'animalImage' => $animalImage,
        ]);
    }

    /**
     * Permet de supprimer un animal
     *
     * @param EntityManagerInterface $manager
     * @param Animal $animal
     * @return Response
     */
    #[Route('/admin/animal/{id}/delete',name:"admin_animal_delete")]
    public function delete(EntityManagerInterface $manager,Animal $animal):Response
    {
        $this->addFlash('danger','Le profil de '.$animal->getName().' a bien été supprimé');

        if(!empty($animal->getCoverImage()))
        {
            unlink($this->getParameter('uploads_directory_animal').'/'.$animal->getCoverImage());
            $animal->setCoverImage('');
            $manager->persist($animal);
        }

        $manager->remove($animal);
        $manager->flush();

        return $this->redirectToRoute('admin_animal_index');
    }
    
    /**
     * Permet d'afficher les animaux avec une recherche / filtre et une pagination
     *
     * @param Request $request
     * @param PaginationFiltreService $pagination
     * @param integer $page
     * @param string $recherche
     * @param string $filtre
     * @return Response
     */
    #[Route('/admin/animal/{page<\d+>?1}/recherche/{recherche}/filtre/{filtre}', name: 'admin_animal_index')]
    public function index(Request $request,PaginationFiltreService $pagination,int $page,string $recherche="vide",string $filtre="vide"): Response
    {
        if($recherche == "vide"){
            $recherche = "";
        }
        if($filtre == "vide"){
            $filtre = "";
        }

        $pagination->setEntityClass(Animal::class)
                    ->setSearch($recherche)
                    ->setFiltre($filtre)
                    ->setPage($page)
                    ->setLimit(10);

        $form = $this->createForm(SearchFiltreType::class,null,[
            'choices' => [
                "" => "",
                "Chat" => "chat",
                "Chien" => "chien",
                "Lapin" => "lapin"
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

        return $this->render('admin/animal/index.html.twig', [
            'pagination' => $pagination,
            'formSearch' => $form->createView(),
            "search" => $recherche,
            "filtre" => $filtre
        ]);
    }
}