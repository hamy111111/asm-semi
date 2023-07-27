<?php

namespace App\Controller;


use App\Entity\Product;
use App\Entity\Category;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// thu vien them
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\String\Slugger\SluggerInterface;
// thu vien quan trong
use App\Form\ProductType;

class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function listAction (ManagerRegistry $doctrine): Response
    {
        $products = $doctrine ->getRepository('App\Entity\Product')->findAll();

        return $this->render('product/index.html.twig', [ 'product'=>$products,
            'controller_name' => $products ,
        ]);
    }
    #[Route('/product/details/{id}', name:"product_details")]
    public function detailsAction(ManagerRegistry $doctrine, $id)
    {
        $products = $doctrine ->getRepository('App\Entity\Product')->find($id);

        return $this->render('product/details.html.twig', [ 'product'=>$products
        #'controller_name' => 'ProductController',;
        ]);
    }
    #[Route('/product/delete/{id}', name:"product_delete")]
    public function deleteAction(ManagerRegistry $doctrine, $id)
    {

       $em = $doctrine->getManager();
       $product = $em->getRepository('App\Entity\Product')->find($id);
       $em->remove($product);
       $em->flush();
       $this->addFlash(
           'error',
           'Product delete'
       );
       return $this->redirectToRoute('app_product');
    }
#[Route('product/create', name: 'product_create', methods:['GET','POST'])]
    public function createAction(ManagerRegistry $doctrine, Request $request, SluggerInterface $slugger)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class,$product);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            // uplpad file
            $productImage = $form->get('image')->getData();
            if ($productImage) {
                $originalFilename = pathinfo($productImage->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $productImage->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $productImage->move(
                        $this->getParameter('thumbnail_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash(
                        'error',
                        'Cannot upload'
                    );// ... handle exception if something happens during file upload
                }
                $product->setImage($newFilename);
            }else{
                $this->addFlash(
                    'error',
                    'Cannot upload'
                );// ... handle exception if something happens during file upload
            }
            $em = $doctrine->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash(
                'notice',
                'Product Added'
            );
            return $this->redirectToRoute('app_product');
        }
        return $this->renderForm('product/create.html.twig', ['form'=>$form]);
    }
#[Route('/product/edit/{id}', name: 'product_edit')]
    public function editAction(ManagerRegistry $doctrine, $id, Request $request, SluggerInterface $slugger)
    {
        $em = $doctrine->getManager();
        $product = $em->getRepository('App\Entity\Product')->find($id);
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // uplpad file
            $productImage = $form->get('image')->getData();
            if ($productImage) {
                $originalFilename = pathinfo($productImage->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $productImage->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $productImage->move(
                        $this->getParameter('thumbnail_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash(
                        'error',
                        'Cannot upload'
                    );// ... handle exception if something happens during file upload
                }
                $product->setImage($newFilename);
            }else{
                $this->addFlash(
                    'error',
                    'Cannot upload'
                );// ... handle exception if something happens during file upload
            }
            $em = $doctrine->getManager();
            $em->persist($product);
            $em->flush();

            $this->addFlash(
                'notice',
                'Product Edited'
            );
            return $this->redirectToRoute('app_product');
        }
        return $this->renderForm('product/edit.html.twig', ['form' => $form, 'id' => $id]);
    }
}

