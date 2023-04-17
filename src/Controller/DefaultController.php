<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DefaultController extends AbstractController
{
   #[Route('/', name: 'show_home', methods: ['GET'])]
   public function showHome(ArticleRepository $repository): Response
   {
      $articles = $repository->findBy(['deletedAt' => null]);

    return $this->render('default/show_home.html.twig', [
      'article' => $articles 
    ]);
   } // end function showHome
}// end class
