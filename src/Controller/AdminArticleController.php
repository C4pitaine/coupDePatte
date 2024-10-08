<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Form\SearchFiltreType;
use App\Form\ArticleModifyType;
use App\Service\PaginationFiltreService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class AdminArticleController extends AbstractController
{
    /**
     * Permet d'ajouter un article
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/admin/article/create', name: 'admin_article_create')]
    public function create(Request $request,EntityManagerInterface $manager):Response
    {
        $article = new Article();

        $form = $this->createForm(ArticleType::class,$article);
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
                        $this->getParameter('uploads_directory_article'), 
                        $newFilename 
                    );
                }catch(FileException $e)
                {
                    return $e->getMessage();
                }
                $article->setImage($newFilename);
            }
            $manager->persist($article);
            $manager->flush();

            $this->addFlash('success','L\'article a bien été ajouté');
            return $this->redirectToRoute('admin_article_index');
        }

        return $this->render('/admin/article/new.html.twig',[
            'myForm' => $form->createView(),
        ]);
    }

    /**
     * Permet de modifier un article
     *
     * @param Article $article
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/admin/article/{id}/update',name: 'admin_article_update')]
    public function update(Article $article,Request $request,EntityManagerInterface $manager): Response
    {
        $articleImage = $article->getImage();
        $article->setImage("");
        $form = $this->createForm(ArticleModifyType::class,$article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $article->setImage($articleImage);
            $file = $form['image']->getData();
            if(!empty($file))
            {
                if(!empty($articleImage)){
                    unlink($this->getParameter('uploads_directory_article').'/'.$articleImage);
                }
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
                $newFilename = $safeFilename."-".uniqid().'.'.$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('uploads_directory_article'), 
                        $newFilename 
                    );
                }catch(FileException $e)
                {
                    return $e->getMessage();
                }
                $article->setImage($newFilename);
            }else{
                if(!empty($articleImage)){
                    $article->setImage($articleImage);
                }
            }

            $manager->persist($article);
            $manager->flush();
            
            $this->addFlash('warning','L\'article : '.$article->getTitle().' a bien été modifié');
            return $this->redirectToRoute('admin_article_index');
        }

        return $this->render('admin/article/update.html.twig',[
            'article' => $article,
            'formArticle' => $form->createView(),
            'articleImage' => $articleImage,
        ]);
    }

    /**
     * Permet de supprimer un article
     *
     * @param Article $article
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/admin/article/{id}/delete',name: 'admin_article_delete')]
    public function delete(Article $article,EntityManagerInterface $manager): Response
    {
        $this->addFlash('danger','L\'article '.$article->getTitle().' a bien été supprimé');

        if(!empty($article->getImage()))
        {
            unlink($this->getParameter('uploads_directory_article').'/'.$article->getImage());
            $article->setImage('');
            $manager->persist($article);
        }

        $manager->remove($article);
        $manager->flush();

        return $this->redirectToRoute('admin_article_index');
    }
    
    /**
     * Permet d'afficher un article
     *
     * @param Article $article
     * @return Response
     */
    #[Route('/admin/article/{id}/show',name: 'admin_article_show')]
    public function show(Article $article): Response
    {
        return $this->render('admin/article/show.html.twig',[
            'article' => $article
        ]);
    }
    
    /**
     * Permet d'afficher les articles avec une recherche et des filtres avec les résultats paginés
     *
     * @param Request $request
     * @param PaginationFiltreService $pagination
     * @param integer $page
     * @param string $recherche
     * @param string $filtre
     * @return Response
     */
    #[Route('/admin/article/{page<\d+>?1}/recherche/{recherche}/filtre/{filtre}', name: 'admin_article_index')]
    public function index(Request $request,PaginationFiltreService $pagination,int $page,string $recherche="vide",string $filtre="vide"): Response
    {
        if($recherche == "vide"){
            $recherche = "";
        }
        if($filtre == "vide"){
            $filtre = "";
        }

        $pagination->setEntityClass(Article::class)
                    ->setSearch($recherche)
                    ->setFiltre($filtre)
                    ->setPage($page)
                    ->setLimit(10);

        $form = $this->createForm(SearchFiltreType::class,null,[
            'choices' => [
                "" => "",
                "Nutrition" => "nutrition",
                "Actualité" => "actualite",
                "Bien-être" => "bien_etre",
                "Santé" => "sante",
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

        return $this->render('admin/article/index.html.twig', [
            'pagination' => $pagination,
            'formSearch' => $form->createView(),
        ]);
    }
}
