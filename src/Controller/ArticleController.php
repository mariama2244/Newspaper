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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

            } // end if photo

        } // end if($form)

        return $this->render('article/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
    
}// end class
