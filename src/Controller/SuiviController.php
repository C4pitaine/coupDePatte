<?php

namespace App\Controller;

use App\Entity\Suivi;
use App\Form\SuiviType;
use App\Repository\AnimalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class SuiviController extends AbstractController
{
    #[Route('/suivi/add', name: 'suivi')]
    public function index(EntityManagerInterface $manager,Request $request,AnimalRepository $repo): Response
    {
        $suivi = new Suivi();
        $form = $this->createForm(SuiviType::class,$suivi);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $file = $form['image']->getData();
            if(!empty($file))
            {
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename."-".uniqid().'.'.$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('uploads_directory_suivi'), 
                        $newFilename 
                    );
                }catch(FileException $e)
                {
                    return $e->getMessage();
                }
                $suivi->setImage($newFilename);
            }
            $manager->persist($suivi);
            $manager->flush();

            $this->addFlash('success','Votre suivi a bien été ajouté');
            return $this->redirectToRoute('account_index');
        }

        return $this->render('suivi/index.html.twig', [
            'myForm' => $form->createView(),
        ]);
    }
}
