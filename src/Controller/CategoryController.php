<?php

namespace App\Controller;

use DateTime;
use App\Entity\Category;
use App\Form\CategoryFormType;
use App\Repository\CategoryRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;

class CategoryController extends AbstractController
{
    #[Route('/ajouter-une-categorie', name: 'app_category', methods: ['GET', 'POST'])]
    public function addCat(Request $request, CategoryRepository $repository, SluggerInterface $slugger): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryFormType::class, $category)
        ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $category->setCreatedAt(new DateTime());
            $category->setUpdatedAt(new DateTime());

            $repository->save($category, true);

            $this->addFlash('success', "l'ajout de la cayégorie a bien été enregistré !");

            return $this->redirectToRoute('show_dashboard');
        }
        return $this->render('category/add_category.html.twig', [
            'form' => $form->createView(),
        ]);
    } // end addCat()

    #[Route('/modifier-categorie', name: 'update_category', methods: ['GET', 'POST'])]
    public function updateCat(Category $category, Request $request, CategoryRepository $resository): Response
    {
        $form = $this->createForm(CategoryFormType::class, $category, [

        ])
        ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $category->setUpdatedAt(new DateTime());
            
            $repository->save($category, true);

            $this->addFlash('success', "la modification aétée bien enregistrée.");
            return $this->redirectToRoute('show_dashboard');
        }  // end if form()

        return $this->render('category/add_category.html.twig', [
            'form' =>$form->createView(),
            'category' => $category,

        ]);
    }

    #[Route('/restaurer-une-categorie/{id}', name: 'restore_category', methods: ['GET'])]
    public function restoreCategory(Category $category, CategoryRepository $repository): Response
    {
        $category->setDeleteAt(null);

        $repository->save($category, true);

        $this->addFlash('success', "La categorie". $category->getName() . "a bien été restauré.");
        return $this->redirectToRoute('show_home');
    }





    #[Route('/supprimer-une-categorie/{id}', name: 'hard_delete_category', methods: ['GET'])]
    public function hardDeleteCategory(Category $category, CategoryRepository $repository): Response
    {
        $repository->remove($category, true);

        $this->addFlash('success', "Le categorie a bien été supprimé définitivement.");
        return $this->redirectToRoute('show_dashboard');
    } // end hardDelete()
} // end class
