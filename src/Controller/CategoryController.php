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
    #[Route('/ajouter-une-categorie', name: 'add_category', methods: ['GET', 'POST'])]
    public function addCat(Request $request, CategoryRepository $repository, SluggerInterface $slugger): Response
    {
        $category = new Category();

        $form = $this->createForm(CategoryFormType::class, $category)
        ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $category->setCreatedAt(new DateTime());
            $category->setUpdatedAt(new DateTime());

            # L'alias nous sevira pour construire l'url d'un article
            $category->setAlias($slugger->slug($category->getName()));

            $repository->save($category, true);

            $this->addFlash('success', "l'ajout de la cayégorie a bien été enregistré !");

            return $this->redirectToRoute('show_dashboard');
        }

        return $this->render('category/add_category.html.twig', [
            'form' => $form->createView(),
        ]);
    } // end addCat()

    //-------------------------------------------------------------------------------------------

    //------------------------------------update category----------------------------------------------

    #[Route('/modifier-categorie/{id}', name: 'update_category', methods: ['GET', 'POST'])]
    public function updateCat(Category $category, Request $request, CategoryRepository $repository, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(CategoryFormType::class, $category, [
              'category' => $category,
        ])
        ->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $category->setUpdatedAt(new DateTime());

            $category->setAlias($slugger->slug($category->getName()));
            
            $repository->save($category, true);

            $this->addFlash('success', "la modification a été bien enregistrée.");
            return $this->redirectToRoute('show_dashboard');
        }  // end if form()

        return $this->render('category/add_category.html.twig', [
            'form' =>$form->createView(),
            'category' => $category,

        ]);
    }// end update

    // -----------------------------------------------------------------------------------------

    //---------------------------soft delete-----------------------------------------------------
    #[Route('/archiever-categorie/{id}', name: 'soft_delete_category', methods: ['GET'])]
    public function softDeleteCat(Category $category, CategoryRepository $repository): Response
    {
        $category->setDeletedAt(new DateTime());

        $repository->save($category, true);

        $this->addFlash('success', "La catégorie " . $category->getName() . "a bien été archivée !");
        return $this->redirectToRoute('show_dashboard');
    } // end deleteCat()
    // --------------------------------------------------------------------------


    //-----------------------------restore category-----------------------------------------
    #[Route('/restaurer-categorie/{id}', name: 'restore_category', methods: ['GET'])]
    public function restoreCat(Category $category, CategoryRepository $repository): Response
    {
        $category->setDeletedAt(null);

        $repository->save($category, true);

        $this->addFlash('success', "La catégorie " . $category->getName() . "a bien été restauré !");
        return $this->redirectToRoute('show_dashboard');
    } // end restoreCat()

    // ---------------------------------------------hard delete----------------------------------------

    #[Route('/supprimer-une-categorie/{id}', name: 'hard_delete_category', methods: ['GET'])]
    public function hardDeleteCategory(Category $category, CategoryRepository $repository): Response
    {
        $repository->remove($category, true);

        $this->addFlash('success', "Le categorie a bien été supprimé définitivement.");
        return $this->redirectToRoute('show_dashboard');
    } // end hardDelete()
} // end class
