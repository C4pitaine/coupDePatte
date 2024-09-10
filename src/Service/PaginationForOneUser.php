<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class PaginationForOneUser{

    /**
     * Le nom de l'entité sur laquelle on veut effectuer une pagination
     *
     * @var string
     */
    private string $entityClass;
    
    /**
     * Le nombre d'enregistrement à récupérer
     *
     * @var integer
     */
    private int $limit = 10;

    /**
     * La page courante
     *
     * @var integer
     */
    private int $currentPage = 1;

    /**
     * Le manager de Doctrine qui nous permet notament de trouver le repository 
     *
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $manager;


    /**
     * Le moteur de template Twig qui va permettre de générer le rendu de la pagination
     *
     * @var Twig\Environment
     */
    private Environment $twig;

    /**
     * Le nom de la route que l'on veut utiliser pour les boutons de navigations
     *
     * @var string
     */
    private $route;

    /**
     * Le chemin vers le template qui contient la pagination
     *
     * @var string
     */
    private $templatePath;

    /**
     * un tableau pour ordonner les résultats
     *
     * @var array|null
     */
    private ?array $order = null;

    /**
     * Variable qui permet de paginer si on fait une recherche
     *
     * @var string
     */
    private string $search;

    /**
     * Variable qui récupère le filtre
     *
     * @var string
     */
    private string $filtre;

    /**
     * Variable qui récupère l'user id
     *
     * @var integer
     */
    private int $user;

    /**
     * Constructeur du service de pagination qui sera appelé par Symfony
     * 
     * N'oubliez pas de configurer votre fichier service.yaml afin que symfony sache quelle valeur utiliser 
     * pour le $templatePath
     *
     * @param EntityManagerInterface $manager
     * @param Environment $twig
     * @param RequestStack $request
     * @param string $templatePath
     */
    public function __construct(EntityManagerInterface $manager, Environment $twig, RequestStack $request, string $templatePath)
    {
        $this->route = $request->getCurrentRequest()->attributes->get('_route');
        $this->manager = $manager;
        $this->twig = $twig;
        $this->templatePath = $templatePath;
    }

    /**
     * Permet de spécifier l'entité sur laquelle on souhaite paginer
     *
     * @param string $entityClass
     * @return self
     */
    public function setEntityClass(string $entityClass): self
    {
        $this->entityClass = $entityClass;
        return $this;
    }

    /**
     * Permet de récupérer l'entité sur laquelle on est en train de paginer
     *
     * @return string
     */
    public function getEntityClass(): string{
        return $this->entityClass;
    }

    /**
     * Sur quoi on fait la recherche
     *
     * @param string $search
     * @return self
     */
    public function setSearch(string $search):self
    {
        $this->search = $search;
        return $this;
    }

    /**
     * Permet de récupérer la recherche
     *
     * @return string
     */
    public function getSearch(): string
    {
        return $this->search;
    }

    /**
     * set le filtre 
     *
     * @param string $filtre
     * @return self
     */
    public function setFiltre(string $filtre): self
    {
        $this->filtre = $filtre;
        return $this;
    }

    /**
     * Récupère le filtre utilisé
     *
     * @return string
     */
    public function getFiltre(): string
    {
        return $this->filtre;
    }

    /**
     * Set le user id
     *
     * @param integer $user
     * @return self
     */
    public function setUserId(int $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Récupère l'user id
     *
     * @return integer
     */
    public function getUser(): int
    {
        return $this->user;
    }

    /**
     * Permet de spécifier le nombre d'enregistrament que l'on souhaite obtenir
     *
     * @param int $limit
     * @return self
     */
    public function setLimit(int $limit): self{
        $this->limit = $limit;
        return $this;
    }

    /**
     * Permet de récupérer le nombre d'enregistrement qui seront renvoyés
     *
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * Permet de spécifier l'ordre que l'on souhaite afficher pour les résultats
     *
     * @param array $myOrder
     * @return self
     */
    public function setOrder(array $myOrder): self
    {
        $this->order = $myOrder;
        return $this;
    }

    /**
     * Permet de récupérer le tableau des order
     *
     * @return array
     */
    public function getOrder(): array
    {
        return $this->order;
    }

    /**
     * Permet de spécifier la page que l'on souhaite afficher
     *
     * @param int $page
     * @return self
     */ 
    public function setPage(int $page): self{
        $this->currentPage = $page;
        return $this;
    }

    /**
     * Permet de récupérer la page qui est actuellement affichée
     *
     * @return int
     */
    public function getPage(): int
    {
        return $this->currentPage;
    }

    /**
     * Permet de récupérer les données paginées pour une entité spécifique
     * @throws Exception si la propriété $entityClass n'est pas définie
     * @return array
     */
    public function getData(): array
    {
        if(empty($this->entityClass))
        {
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer! Utilisez la méthode setEntityClass() de votre objet PaginationService");
        }

        // calculer l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;
        // demander au repository de trouver les éléments 
        // $repo = $this->manager->getRepository($this->entityClass);
        // $data = $repo->findBy([],[],$this->limit,$offset);

        // // renvoyer les données
        // return $data;
                        
        if(!$this->search && !$this->filtre){
            return $this->manager
                        ->getRepository($this->entityClass)
                        ->search("","",$this->user,$this->limit,$offset);
        }else if($this->filtre && !$this->search){
            return $this->manager
            ->getRepository($this->entityClass)
            ->search("",$this->filtre,$this->user,$this->limit,$offset);
        }else{
            return $this->manager
                        ->getRepository($this->entityClass)
                        ->search($this->search,$this->filtre,$this->user,$this->limit,$offset);
        }
    }


    /**
     * Permet de récupérer le nombre de page qui existent sur une entité particulière
     *
     * @throws Exception si la propriété $entityClass n'est pas configurée
     * @return integer
     */
    public function getPages(): int
    {
        if(empty($this->entityClass))
        {
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer! Utilisez la méthode setPages() de votre objet PaginationService");
        }
        // conntaire le total des enregirstement de la tablez
        // $repo = $this->manager->getRepository($this->entityClass);
        // $total = count($repo->findAll());
        if(!$this->search && !$this->filtre){
            $total = count($this->manager->getRepository($this->entityClass)->search("","",$this->user));
        }else{
            $total = count($this->manager->getRepository($this->entityClass)->search($this->search,$this->filtre,$this->user));
        }
        if($total === 0){
            $total = $total+1;
        }
  
        return ceil($total / $this->limit);;
    }

    /**
     * Permet d'afficher le rendu de la navigation au sein d'un template Twig
     * On se sert ici de notre moteur de rendu afin de compiler le template qui se trouve au chemin de notre propriété
     * $templatePath, en lui passant les variables page, pages et route 
     *
     * @return void
     */
    public function display(): void
    {
        $pages = $this->getPages();
        $page = $this->currentPage;
        if($pages > 5){
            if($page == 1){
                $pageMax = 3;
                $pageMin = $page;
            }elseif($page == $pages){
                $pageMax = $pages;
                $pageMin = $page - 2;
            }else{
                $pageMax = ($page + 1);
                $pageMin = $page - 1;
            }
        }else{
            $pageMin = 1;
            $pageMax = 5;
        }

        $this->twig->display($this->templatePath, [
            'page' => $page,
            'pages' => $pages,
            'route'=>$this->route,
            'pageMin' => $pageMin,
            'pageMax' => $pageMax,
            'search' => $this->search,
            'filtre' => $this->filtre
        ]);
        
    }

    /**
     * Permet de choisir un template de pagination
     *
     * @param string $templatePath
     * @return self
     */
    public function setTemplatePath(string $templatePath): self {
        $this->templatePath = $templatePath;

        return $this;
    }

    /**
     * Permet de récupérer le templatePath actuellement utilisé
     *
     * @return string
     */
    public function getTemplatePath(): string{
        return $this->templatePath;
    }

    /**
     * Permet de changer la route par défaut pour les liens de la navigation
     *
     * @param string $route
     * @return self
     */
    public function setRoute(string $route): self{
        $this->route = $route;

        return $this;
    }

    /**
     * Permet de récupérer le nom de la route qui sera utilisée sur les liens de la pagination
     *
     * @return string
     */
    public function getRoute(): string {
        return $this->route;
    }
}