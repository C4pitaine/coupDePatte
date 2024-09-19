<?php

namespace App\Controller;

use App\Entity\Favori;
use App\Entity\PasswordUpdate;
use App\Form\SearchFiltreType;
use App\Form\PasswordUpdateType;
use App\Form\UserUpdateType;
use App\Service\PaginationForOneUser;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AccountController extends AbstractController
{
    /**
     * Permet d'afficher les favoris d'un User
     *
     * @param Request $request
     * @param PaginationForOneUser $pagination
     * @param integer $page
     * @param string $recherche
     * @param string $filtre
     * @return Response
     */
    #[Route('/account/{page<\d+>?1}/recherche/{recherche}/filtre/{filtre}', name: 'account_index')]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request,PaginationForOneUser $pagination,int $page,string $recherche="vide",string $filtre="vide"): Response
    {
        $user = $this->getUser();

        if($recherche == "vide"){
            $recherche = "";
        }
        if($filtre == "vide"){
            $filtre = "";
        }

        $pagination->setEntityClass(Favori::class)
                    ->setSearch($recherche)
                    ->setFiltre($filtre)
                    ->setUserId($user->getId())
                    ->setPage($page)
                    ->setLimit(4)
                    ->setTemplatePath('/partials/_paginationFront.html.twig');

        $form = $this->createForm(SearchFiltreType::class,null,[
            'choices' => [
                "" => "",
                "Chien" => "chien",
                "Chat" => "chat",
                "Lapin" => "lapin",
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

        return $this->render('account/index.html.twig', [
            'pagination' => $pagination,
            'formSearch' => $form->createView(),
            'user' => $user,
        ]);
    }

     /**
     * Permet à l'utilisateur de modifier son mot de passe
     *
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @param UserPasswordHasherInterface $hasher
     * @return Response
     */
    #[Route("/profile/passwordUpdate", name:"account_passwordUpdate")]
    #[IsGranted("ROLE_USER")]
    public function updatePassword(EntityManagerInterface $manager,Request $request,UserPasswordHasherInterface $hasher): Response
    {
        $passwordUpdate = new PasswordUpdate();
        $user = $this->getUser();

        $form = $this->createForm(PasswordUpdateType::class,$passwordUpdate);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            if(!password_verify($passwordUpdate->getOldPassword(),$user->getPassword()))
            {
                $form->get('oldPassword')->addError(new FormError('Erreur dans le mot de passe actuel'));
            }else{
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $hasher->hashPassword($user,$newPassword);

                $user->setPassword($hash);
                $manager->persist($user);
                $manager->flush();

                $this->addFlash('success','Votre mot de passe a bien été modifié');
                return $this->redirectToRoute('account_index');
            }
        }

        return $this->render('account/passwordUpdate.html.twig',[
            'myForm' => $form->createView(),
        ]);
    }

    /**
     * Permet à l'utilisateur de modifier ses informations
     *
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    #[Route('/account/userUpdate',name:'account_userUpdate')]
    #[IsGranted("ROLE_USER")]
    public function updateUser(EntityManagerInterface $manager,Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserUpdateType::class,$user);
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success','Vos informations ont bien été modifiées');
            return $this->redirectToRoute('account_index');
        }

        return $this->render('account/userUpdate.html.twig',[
            'myForm' => $form->createView(),
        ]);
    }
}
