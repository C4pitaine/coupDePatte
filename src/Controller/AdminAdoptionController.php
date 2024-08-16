<?php

namespace App\Controller;

use App\Entity\Animal;
use App\Entity\Adoption;
use App\Entity\Indispensable;
use App\Form\SearchFiltreType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;
use App\Service\PaginationFiltreService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdoptionController extends AbstractController
{
    /**
     * Permet d'afficher un animal à faire adopter
     *
     * @param Adoption $adoption
     * @return Response
     */
    #[Route('/admin/adoption/{id}/show',name:'admin_adoption_show')]
    public function show(Adoption $adoption):Response
    {
        return $this->render('admin/adoption/show.html.twig',[
            'adoption' => $adoption,
        ]);
    }

    /**
     * Permet de supprimer une demande d'adoption
     *
     * @param EntityManagerInterface $manager
     * @param Adoption $adoption
     * @return Response
     */
    #[Route('/admin/adoption/{id}/delete',name:'admin_adoption_delete')]
    public function delete(EntityManagerInterface $manager,Adoption $adoption): Response
    {
        $this->addFlash('danger',"La demande d'adoption pour ".$adoption->getName()." a bien été supprimée");
        
        if(!empty($adoption->getImage()))
        {
            unlink($this->getParameter('uploads_directory_animal').'/'.$adoption->getImage());
            $adoption->setImage('');
            $manager->persist($adoption);
        }

        $manager->remove($adoption);
        $manager->flush();

        return $this->redirectToRoute('admin_adoption_index');
    }

    #[Route('/asmin/adoption/{id}/transfer',name:'admin_adoption_transfer')]
    public function transfer(Adoption $adoption,EntityManagerInterface $manager): Response
    {
        $this->addFlash('success','Le profil de '.$adoption->getName().' a bien été transféré');

        $animal = new Animal();

        $animal->setName($adoption->getName())
                ->setType($adoption->getType())
                ->setGenre($adoption->getGenre())
                ->setAge($adoption->getAge())
                ->setRace($adoption->getRace())
                ->setDescription($adoption->getDescription())
                ->setAdoptionDate(null)
                ->setAdopted(false)
                ->setCoverImage($adoption->getImage());
                foreach($adoption->getIndispensables() as $indispensable)
                {
                    $animal->addIndispensable($indispensable);
                }

        $manager->persist($animal);
        $manager->remove($adoption);
        $manager->flush();

        return $this->redirectToRoute('admin_adoption_index');
    }

    /**
     * Permet d'afficher les demandes d'adoptions avec une recherche et des filtres avec les résultats paginés
     *
     * @param Request $request
     * @param PaginationFiltreService $pagination
     * @param integer $page
     * @param string $recherche
     * @param string $filtre
     * @return Response
     */
    #[Route('/admin/adoption/{page<\d+>?1}/recherche/{recherche}/filtre/{filtre}', name: 'admin_adoption_index')]
    public function index(Request $request,PaginationFiltreService $pagination,int $page,string $recherche="vide",string $filtre="vide"): Response
    {
        if($recherche == "vide"){
            $recherche = "";
        }
        if($filtre == "vide"){
            $filtre = "";
        }

        $pagination->setEntityClass(Adoption::class)
                    ->setSearch($recherche)
                    ->setFiltre($filtre)
                    ->setPage($page)
                    ->setLimit(10);

        $form = $this->createForm(SearchFiltreType::class);
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

        return $this->render('admin/adoption/index.html.twig', [
            'pagination' => $pagination,
            'formSearch' => $form->createView(),
        ]);
    }
}
