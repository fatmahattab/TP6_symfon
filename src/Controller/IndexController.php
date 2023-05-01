<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Entity\Article;
use App\Entity\Category;
use App\Form\CategoryType;

use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Form\ArticleType;

class IndexController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
      
       $this->entityManager = $entityManager;
      
 
    }
 /**
    * @Route("/",name="article_list")
    */
    public function home()
    {
       //récupérer tous les articles de la table article de la BD
       // et les mettre dans le tableau $articles
       $articles= $this->entityManager->getRepository(Article::class)->findAll();
       return $this->render('articles/index.html.twig',['articles' => $articles]);
    }
 
 /**
    * @Route("/article/save")
    */
    public function save(Request $request): Response
    {
       $article = new Article();
       $article->setNom('Article 2');
       $article->setPrix(550);
 
       $this->entityManager->persist($article);
       $this->entityManager->flush();
       return new Response('Article enregisté avec id '.$article->getId());
    }

    /**
 * @Route("/article/new", name="new_article")
 * Method({"GET", "POST"})
 */
 public function new(Request $request) {
   $article = new Article();
   $form = $this->createForm(ArticleType::class,$article);
   $form->handleRequest($request);
   if($form->isSubmitted() && $form->isValid()) {
      $article = $form->getData();
     
      $entityManager = $this->entityManager;
      $entityManager->persist($article);
      $entityManager->flush();
     
      return $this->redirectToRoute('article_list');
      }
      return $this->render('articles/new.html.twig',['form' => $form->createView()]);
  }


  /**
 * @Route("/article/{id}", name="article_show")
 */
public function show($id) {
    $article = $this->entityManager->getRepository(Article::class)
    ->find($id);
    return $this->render('articles/show.html.twig',
    array('article' => $article));
     }

     /**
 * @Route("/article/edit/{id}", name="edit_article")
 * Method({"GET", "POST"})
 */
public function edit(Request $request, $id,EntityManagerInterface $entityManager) {
   $article = new Article();
   $article = $entityManager->getRepository(Article::class)->find($id);
    
    $form = $this->createForm(ArticleType::class,$article);
   
    $form->handleRequest($request);
    if($form->isSubmitted() && $form->isValid()) {
   
    $entityManager = $this->entityManager;
    $entityManager->flush();
   
    return $this->redirectToRoute('article_list');
    }
    return $this->render('articles/edit.html.twig', ['form' => $form->createView()]);
     }

      /**
 * @Route("/article/delete/{id}",name="delete_article")
 * @Method({"DELETE"})
 */
public function delete(Request $request, $id) {
    $article = $this->entityManager->getRepository(Article::class)->find($id);
   
    $entityManager = $this->entityManager;
    $entityManager->remove($article);
    $entityManager->flush();
   
    $response = new Response();
    $response->send();
    return $this->redirectToRoute('article_list');
    }

    /**
     * @Route("/category/newCat", name="new_category")
     * Method({"GET", "POST"})
     */
public function newCategory(Request $request) 
{
   $category = new Category();
    $form = $this->createForm(CategoryType::class,$category); 
    $form->handleRequest($request); 
    if($form->isSubmitted() && $form->isValid()) {
       $article = $form->getData(); 
       
       $this->entityManager->persist($category);
       $this->entityManager->flush();
       } 
       return $this->render('articles/newCategory.html.twig',['form'=> $form->createView()]); }
}?>