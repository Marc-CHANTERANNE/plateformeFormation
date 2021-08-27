<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Entity\Formation;
use App\Entity\Article;
use App\Form\ArticleFormType;
use App\Form\FormationFormType;

class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/admin/listeFormation", name="listeFormation")
     */
    public function listeFormation()
    {
        $formations = $this->getDoctrine()->getRepository(Formation::class)->findAll();

        return $this->render('admin/listeFormation.html.twig', [
            'formations' => $formations
        ]);
    }

    /**
     * @Route("/admin/listeArticle", name="listeArticle")
     */
    public function listeArticle()
    {
        $articles = $this->getDoctrine()->getRepository(Article::class)->findAll();

        return $this->render('admin/listeArticle.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/deleteArticle/{id}", name="articleDelete")
     */
    public function deleteArticle(Article $article)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($article);
        $entityManager->flush();
        return $this->redirectToRoute('listeArticle');
    }

    /**
     * @Route("/deleteFormation/{id}", name="formationDelete")
     */
    public function deleteFormation(Formation $formation)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($formation);
        $entityManager->flush();
        return $this->redirectToRoute('listeFormation');
    }

    /**
     * @Route("admin/listeArticle/modifArticle{id}", name="modifArticle")
     */
    public function modifArticle(Article $article, Request $request, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ArticleFormType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageArticle = $form->get('image')->getData();
            if ($imageArticle) {
                $originalFileName = pathinfo($imageArticle->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalFileName);
                $newFileName = $safeFileName.'-'.uniqid().'.'.$imageArticle->guessExtension();

                try {
                    $imageArticle->move(
                        $this->getParameter('articles_directory'),
                        $newFileName
                    );
                } catch (FileException $e) {
                    
                }

                $article->setImage($newFileName);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('listeArticle');
            $this->addFlash('success', 'Votre article a bien été ajouté !');
            
        }

        return $this->render('admin/modifArticle.html.twig', [
            'modifArticleForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("admin/listeFormation/modifFormation{id}", name="modifFormation")
     */
    public function modifFormation(Formation $formation, Request $request, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(FormationFormType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFormation = $form->get('icon')->getData();
            if ($imageFormation) {
                $originalFileName = pathinfo($imageFormation->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFileName = $slugger->slug($originalFileName);
                $newFileName = $safeFileName.'-'.uniqid().'.'.$imageFormation->guessExtension();

                try {
                    $imageFormation->move(
                        $this->getParameter('formations_directory'),
                        $newFileName
                    );
                } catch (FileException $e) {
                    
                }

                $formation->setIcon($newFileName);
            }
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('listeFormation');
            $this->addFlash('success', 'Votre formation a bien été ajoutée !');
            
        }

        return $this->render('admin/modifFormation.html.twig', [
            'modifFormationForm' => $form->createView(),
        ]);
    }
}
