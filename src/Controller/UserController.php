<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Component\Mime\Email;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
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

            // $email = (new Email())
            //             ->from("")
            //             ->to($user->getEmail())
            //             ->subject("Confirmation de votre addresse email")
            //             ->text("")
            //             ->html('');
            // $mailer->send($email);

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

}
