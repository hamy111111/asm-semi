<?php

namespace App\Controller;

use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// thu vien them
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
// thu vien quan trong
use App\Form\CategoryType;


class CategoryController extends AbstractController
{
    #[Route('/category', name: 'app_category')]
    public function listAction (ManagerRegistry $doctrine): Response
    {
        $categories = $doctrine -> getRepository('App\Entity\Category')->findAll();

        return $this->render('category/index.html.twig', [
            'categories'=>$categories,
        ]);
    }
    #[Route('/category/details/{id}', name:"category_details")]
    public function detailsAction(ManagerRegistry $doctrine, $id)
    {
        $categories = $doctrine ->getRepository('App\Entity\Category')->find($id);

        return $this->render('category/details.html.twig', [ 'category'=>$categories
            #'controller_name' => 'ProductController',;
        ]);
    }
    #[Route('/category/delete/{id}', name:"category_delete")]
    public function deleteAction(ManagerRegistry $doctrine, $id)
    {

        $em = $doctrine->getManager();
        $categories = $em->getRepository('App\Entity\Category')->find($id);
        $em->remove($categories);
        $em->flush();
        $this->addFlash(
            'error',
            'Category delete'
        );
        return $this->redirectToRoute('category_list');
    }
    #[Route('category/create', name: 'category_create', methods:['GET','POST'])]
    public function createAction(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger)
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class,$category);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $em = $doctrine->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash(
                'notice',
                'Category Added'
            );
            return $this->redirectToRoute('app_category');
        }
        return $this->renderForm('category/create.html.twig', ['form'=>$form]);
    }

    #[Route('/category/edit/{id}', name: 'category_edit')]
    public function editAction(ManagerRegistry $doctrine, $id, Request $request)
    {
        $em = $doctrine->getManager();
        $category = $em->getRepository('App\Entity\Category')->find($id);
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em = $doctrine->getManager();
            $em->persist($category);
            $em->flush();

            $this->addFlash(
                'notice',
                'Category Edited'
            );
            return $this->redirectToRoute('app_category');
        }
        return $this->renderForm('category/edit.html.twig', ['form' => $form, 'id' => $id]);
    }
}
