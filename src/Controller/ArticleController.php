<?php

namespace App\Controller;

use DateTime;

use App\Entity\Article;
use App\Form\ArticleFormType;
use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

#[Route('/admin')]
class ArticleController extends AbstractController
{
    #[Route('/ajouter-un-article', name: 'create_article', methods: ['GET', 'POST'])]
    public function createArticle(ArticleRepository $repository, Request $request, SluggerInterface $slugger): Response
    {
       
        $article = new Article();

        $form = $this->createForm(ArticleFormType::class, $article)
        ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
             
            $article->setCreatedAt(new DateTime());
            $article->setUpdatedAt(new DateTime());

            $article->setAlias($slugger->slug($article->getTitle()));

            # set de la relation article et user
            $article->setAuthor($this->getUser());

            $photo = $form->get('photo')->getData();

            if($photo) {
                $this->handleFile($photo, $article, $slugger);
            } // end if photo
            

            $repository->save($article, true);

            $this->addFlash('success', "L'article a bien été modifier  avec succès !");
            return $this->redirectToRoute('show_dashboard');

        } // end if($form)

        return $this->render('article/create.html.twig', [
            'form' => $form->createView(),
           
        ]);
    }// end create()

    #[Route('/modifier-un-article/{id}', name: 'update_article', methods: ['GET', 'POST'])]
    public function updateArticle(Article $article, Request $request, ArticleRepository $repository, SluggerInterface $slugger): Response 
    {
     # Récupération de la photo actuelle
     $currentPhoto = $article->getPhoto();

     $form = $this->createForm(ArticleFormType::class, $article, [
         'photo' => $currentPhoto
     ])
         ->handleRequest($request);
 
         if($form->isSubmitted() && $form->isValid()) {
 
             $article->setUpdatedAt(new DateTime());
             $article->setAlias($slugger->slug($article->getTitle()));            

             $photo = $form->get('photo')->getData();
 
             if($photo) {
                 $this->handleFile($photo, $article, $slugger);
 
             }else{
                 # si pas de nouvelle photo, on resset la photo courante (actuelle).
                 $article->setPhoto($currentPhoto);
             // end if($newPhoto)
             }
             $repository->save($article, true);

             $this->addFlash('success', "L'article a bien été modifier  avec succès !");
             return $this->redirectToRoute('show_dashboard');
         } //end if($form)
 
         return $this->render('article/create.html.twig', [
             'form' => $form->createView(),
             'article' => $article
         ]);
    } // end updateArticle()


    // ------------------------------ SOFT-DELETE-ARTICLE -------------------------------
    #[Route('/archiver-un-article/{id}', name: 'soft_delete_article', methods: ['GET'])]
    public function softDeleteArticle(Article $article, ArticleRepository $repository): Response
    {
        $article->setDeletedAt(new DateTime());

        $repository->save($article, true);

        $this->addFlash('success', "L'article a bien été archivé !");
        return $this->redirectToRoute('show_dashboard');
    } // end softDeleteArticle()
    // ----------------------------------------------------------------------------------



    // -------------------------------- RESTORE-ARTICLE ---------------------------------
    #[Route('/restaurer-article/{id}', name: 'restore_article', methods: ['GET'])]
    public function restoreArticle(Article $article, ArticleRepository $repository): Response
    {
        $article->setDeletedAt(null);

        $repository->save($article, true);

        $this->addFlash('success', "L'article a bien été restauré !");
        return $this->redirectToRoute('show_dashboard');
    } // end restoreArticle()
    // ----------------------------------------------------------------------------------



    // ------------------------------ HARD-DELETE-ARTICLE -------------------------------
    #[Route('/supprimer-article/{id}', name: 'hard_delete_article', methods: ['GET'])]
    public function hardDeleteArticle(Article $article, ArticleRepository $repository): Response
    {
        $repository->remove($article, true);

        $this->addFlash('success', "L'article a bien été supprimé définitivement !");
        return $this->redirectToRoute('show_dashboard');
    } // end hardDeleteArticle()
    // ----------------------------------------------------------------------------------


    private function handleFile(UploadedFile $photo, Article $article, SluggerInterface $slugger)
    {
        $extension = '.' . $photo->guessExtension();
        $safeFilename = $slugger->slug(pathinfo($photo->getClientOriginalExtension(), PATHINFO_FILENAME));

        $newFilename = $safeFilename . '-' . uniqid() . $extension;

        try{
            $photo->move($this->getParameter('uploads_dir'), $newFilename);
            $article->setPhoto($newFilename);
        } catch (FileException $exception) {
            // code à exécuter en cas d'erreur
        }
    } // end handleFile()
    
}// end class
