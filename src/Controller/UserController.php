<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\TooManyLoginAttemptsAuthenticationException;

class UserController extends AbstractController
{
    /**
     * Permet de se connecter
     *
     * @param AuthenticationUtils $utils
     * @return Response
     */
    #[Route('/login', name: 'account_login')]
    public function index(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        $loginError = null;
        $emailError = null;

        if($error instanceof TooManyLoginAttemptsAuthenticationException){
            $loginError = "Trop de tentatives de connexion. Veuillez attendre 15minutes";
        }
        if($error instanceof CustomUserMessageAuthenticationException){
            $emailError = "Veuillez confirmer votre email";
        }

        return $this->render('user/index.html.twig', [
            'error' => $error !==null,
            'username' => $username,
            'loginError' => $loginError,
            'emailError' => $emailError
        ]);
    }

    /**
     * Permet à l'utilisateur de se déconnecter
     *
     * @return void
     */
    #[Route('/logout',name:'account_logout')]
    public function logout(): void 
    {}

    /**
     * Permet à l'utilisateur de s'inscrire
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordHasherInterface $hash
     * @param MailerInterface $mailer
     * @return Response
     */
    #[Route('/register',name:'account_register')]
    public function register(Request $request,EntityManagerInterface $manager,UserPasswordHasherInterface $hasher,MailerInterface $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class,$user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $hash = $hasher->hashPassword($user,$user->getPassword());
            $user->setPassword($hash);
            $user->setFamilleAccueil(false);
            $user->setChecked(false);
            $salt = rand(1,1000);
            $token = md5($user->getEmail().$salt);
            $user->setToken($token);
            $manager->persist($user);
            $manager->flush();

            $email = (new Email())
                        ->from("noreply@coupdepatte.alexandresacre.com")
                        ->to($user->getEmail())
                        ->subject("Confirmation de votre addresse email")
                        ->text("
                            Coup de patte - Refuge animalier
                            Confirmez votre adresse email pour pouvoir vous connecter
                            Confirmer votre email:https://coupdepatte.alexandresacre.com/register/".$user->getId()."/t/".$token."
                        ")
                        ->html('
                            <h1>Coup de patte - Refuge animalier</h1>
                            <p>Confirmez votre adresse email pour pouvoir vous connecter</p>
                            <a href="https://coupdepatte.alexandresacre.com/register/'.$user->getId()."/t/".$token.'">Confirmer votre email</a>
                        ');
            $mailer->send($email);

            $this->addFlash('success','Inscription réussie,Veuillez confirmer votre email avant de pouvoir vous connecter,Vérifiez vos courriers indésirables');
            return $this->redirectToRoute('account_login');
        }

        return $this->render('user/registration.html.twig',[
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * Permet à l'utilisateur de confirmer son Email 
     *
     * @param UserRepository $repo
     * @param EntityManagerInterface $manager
     * @param integer $id
     * @param string $token
     * @return Response
     */
    #[Route('/register/{id}/t/{token}',name:'account_checkEmail')]
    public function confirmEmail(UserRepository $repo,EntityManagerInterface $manager,int $id,string $token): Response
    {
        $user = $repo->findOneBy(['id'=>$id]);
        if($user){
            if($user->isChecked()){
                $message = "Votre adresse E-mail a déjà été confirmée ✅";
            }else{
                $checkToken = $user->getToken() == $token;
                if($checkToken){
                    $user->setChecked(true);
                    $manager->persist($user);
                    $manager->flush();

                    $message = "Votre adresse E-mail a été confirmée ✅";
                }else{
                    throw new BadRequestException('Token invalide');
                }
            }
        }else{
            throw new BadRequestException('Id invalide');
        }

        return $this->render('user/checkEmail.html.twig',[
            'message'=>$message
        ]);
    }

    #[Route('/user/reset/request',name:"user_reset_request")]
    public function resetRequest(UserRepository $repo,Request $request,EntityManagerInterface $manager,UserPasswordHasherInterface $hasher,MailerInterface $mailer): Response
    {
        $error = null;

        if($request->isMethod('POST'))
        {
            if($request->request->get('email')){
                $user = $repo->findOneBy(['email'=>$request->request->get('email')]);
                if($user){
                    $chaine = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
                    $chaine = str_shuffle($chaine);
                    $chaine = substr($chaine,0,24);

                    $hash = $hasher->hashPassword($user,$chaine);
                    $user->setPassword($hash);

                    $manager->persist($user);
                    $manager->flush();
                    
                    $email = (new Email())
                        ->from("noreply@coupdepatte.alexandresacre.com")
                        ->to($user->getEmail())
                        ->subject("Réinitialisation de votre mot de passe")
                        ->text('
                            Coup de patte - Refuge animalier</h1>
                            Réinitialisation de votre mot de passe
                            Voici votre nouveau mot de passe : '.$chaine.'
                            N\'oubliez pas de le modifier après vous être connecté
                            Réiniatialiser votre mot de passe : https://coupdepatte.alexandresacre.com/user/reset/'.$user->getEmail()."/password/t/".$user->getToken().'
                        ')
                        ->html('
                            <h1>Coup de patte - Refuge animalier</h1>
                            <p>Réinitialisation de votre mot de passe</p>
                            <p>Voici votre nouveau mot de passe : '.$chaine.'</p>
                            <p>N\'oubliez pas de le modifier</p>
                            <a href="https://coupdepatte.alexandresacre.com/user/reset/'.$user->getEmail()."/password/t/".$user->getToken().'">Réinitialiser votre mot de passe</a>
                        ');
            $mailer->send($email);

                    $this->addFlash('success',"Votre nouveau mot de passe a été envoyé par e-mail. N'oubliez pas de vérifier vos courriers indésirables.");
                    return $this->redirectToRoute('account_login');
                }else{
                    $error = "Cette adresse email n'est pas associée à un compte inscrit.";
                }
            }else{
                $error = "Le champ de l'adresse e-mail ne peut pas être vide";
            }
        }

        return $this->render('user/resetRequest.html.twig',[
            'error' => $error,
        ]);
    }

    #[Route('/user/reset/{email}/password/t/{token}',name:'reset_password')]
    public function resetPassword(EntityManagerInterface $manager,Request $request,UserPasswordHasherInterface $hasher,UserRepository $repo,string $email,string $token):Response
    {   
        $user = $repo->findOneBy(['email'=>$email]);

        if($user){
            if($token == $user->getToken()){
                $passwordUpdate = new PasswordUpdate();

                $form = $this->createForm(PasswordUpdateType::class,$passwordUpdate);
                $form->handleRequest($request);

                if($form->isSubmitted() && $form->isValid()){
                    
                    if(!password_verify($passwordUpdate->getOldPassword(),$user->getPassword()))
                    {
                        $form->get('oldPassword')->addError(new FormError('Erreur dans le mot de passe envoyé par mail'));
                    }else{
                        $newPassword = $passwordUpdate->getNewPassword();
                        $hash = $hasher->hashPassword($user,$newPassword);

                        $user->setPassword($hash);
                        $manager->persist($user);
                        $manager->flush();

                        $this->addFlash('success','Votre mot de passe a bien été modifié');
                        return $this->redirectToRoute('account_login');
                    }
                }
            }else{
                throw new BadRequestException('Token invalide');
            }
        }else{
            throw new BadRequestException('Email non inscrit');
        }

        return $this->render('user/passwordUpdate.html.twig',[
            'myForm' => $form->createView(),
        ]);
    }

}
