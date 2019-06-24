<?php

namespace App\Controller;
use App\Entity\Category;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Repository\ArticleRepository;
use App\Entity\Articles;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;


class BlogController extends AbstractController{



    /**
     * @Route("/blog", name="blog")
        */
    public function index(){   
            $repo = $this->getDoctrine()->getRepository(Articles::class); 
            $allArticles = $repo->findAll();
            return $this->render('blog/index.html.twig',['articles'=>$allArticles]);
    }



    /**
    * @Route("/", name="home")
    */
    //si dans route on met un simple / cela va être lu comme étant la page par défaut par symfony
    public function home(){
            return $this->render('blog/home.html.twig') ;
    }




        /**
    * @Route("/blog/new", name="blog_create")
    * @Route("/blog/{id}/edit", name="blog_edit")
    */
    public function create(Articles $article = null, Request $request, ObjectManager $manager){
    /// injection de dépendance Request (get, post etc...), object manager
            dump($request) ;

            if(!$article){
                $article = new Articles ;
            }

            $form = $this->createForm(ArticleType::class, $article);

           // // on fait appel à l'entité "Article" ->la table de la bdd

             //$form = $this->createFormBuilder($article)
             // ->add('title')
             //  ->add('content')
             // ->add('img')
             // ->getForm();
        
            if(!$article->getId()){ 
                $article->setCreatedAt(new \DateTime()) ;	
            }

                $form->handleRequest($request);


            if($form->isSubmitted() && $form->isValid()){
            ////"SI formulaire est envoyé et SI il est valide fait partir
            
	            $manager->persist($article) ;
                $manager->flush() ;

                return $this->redirectToRoute('blog_show' , ['id'=>$article->getId()]);
            //ici, on demande à symfony d'ouvrir l'article qu'on vient de crééer au moment de le submit via ->redirectToRoute. On y ajoute le name de la page vers quoi on veut aller name="blog_show" (notre fonction show sur ce controller avec le name)

        }

        return $this->render('blog/create.html.twig',[
        'formArticle'=>$form->createView(),
        'editMode'=>$article->getId()
        ]) ;
    }


         /**
    * @Route("/blog/form", name="blog_form")
    * @Route("/blog/form/{id}/edit", name="blog_edit")
    */
    public function formu(Articles $article = null, Request $request, ObjectManager $manager){
            dump($request) ;

            if(!$article){
            $article = new Articles ;
            }

            $form = $this->createForm(ArticleType::class, $article);

        //$form = $this->createFormBuilder($article)
           // ->add('title')
          //  ->add('content')
           // ->add('img')
           // ->getForm();
        
            if(!$article->getId()){ 
                $article->setCreatedAt(new \DateTime()) ;	
            }

                $form->handleRequest($request);

        //dump($article) ;

            if($form->isSubmitted() && $form->isValid()){
            
	            $manager->persist($article) ;
                $manager->flush() ;

                return $this->redirectToRoute('blog_show' , ['id'=>$article->getId()]);

            }

        return $this->render('blog/create.html.twig',[
        'formArticle'=>$form->createView(),
        'editMode'=>$article->getId()
        ]) ;
    }
    

     


    /**
    * @Route("/blog/{id}", name="blog_show") 
    */
    public function show($id, Articles $article, Request $request, ObjectManager $manager){
        $repo = $this->getDoctrine()->getRepository(Articles::class); 
        $article = $repo->find($id);

            $comment = new Comment();
            $form = $this->createForm(CommentType::class, $comment);


            if($request){ 
            //  dump($request);
                $comment->setCreatedAt(new \Datetime())
                ->setArticle($article);	
            }
  
            $form->handleRequest($request);


            if($form->isSubmitted() && $form->isValid()){

                dump($request);

                $manager->persist($comment);
                $manager->flush();

                return $this->redirectToRoute('blog_show' , ['id'=>$article->getId()]);
            }
        
        return $this->render('blog/show.html.twig',[
            'article'=>$article,
            'commentForm'=> $form->createView()
            
            ]);
    }




}
