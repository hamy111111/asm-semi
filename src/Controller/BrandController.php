<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Form\BrandType;
use App\Form\CategoryType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BrandController extends AbstractController
{
    #[Route('/brand', name: 'app_brand')]
    public function listAction (ManagerRegistry $doctrine): Response
    {
        $brands = $doctrine -> getRepository('App\Entity\Brand')->findAll();

        return $this->render('brand/index.html.twig', [
            'brands'=>$brands,
        ]);
    }
    #[Route('/brand/details/{id}', name:"brand_details")]
    public function detailsAction(ManagerRegistry $doctrine, $id)
    {
        $brands = $doctrine ->getRepository('App\Entity\Brand')->find($id);

        return $this->render('brand/details.html.twig', [ 'brand'=>$brands
            #'controller_name' => 'ProductController',;
        ]);
    }
    #[Route('/brand/delete/{id}', name:"brand_delete")]
    public function deleteAction(ManagerRegistry $doctrine, $id)
    {

        $em = $doctrine->getManager();
        $brands = $em->getRepository('App\Entity\Brand')->find($id);
        $em->remove($brands);
        $em->flush();
        $this->addFlash(
            'error',
            'Brand delete'
        );
        return $this->redirectToRoute('app_brand');
    }
    #[Route('/brand/create', name: 'brand_create', methods:['GET','POST'])]
    public function createAction(ManagerRegistry $doctrine, Request $request)
    {
        $brands = new Brand();
        $form = $this->createForm(BrandType::class,$brands);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $em = $doctrine->getManager();
            $em->persist($brands);
            $em->flush();

            $this->addFlash(
                'notice',
                'Brand Added'
            );
            return $this->redirectToRoute('app_brand');
        }
        return $this->renderForm('brand/create.html.twig', ['form'=>$form]);
    }

    #[Route('/brand/edit/{id}', name: 'brand_edit')]
    public function editAction(ManagerRegistry $doctrine, $id, Request $request)
    {
        $em = $doctrine->getManager();
        $brand = $em->getRepository('App\Entity\Brand')->find($id);
        $form = $this->createForm(BrandType::class, $brand);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $em = $doctrine->getManager();
            $em->persist($brand);
            $em->flush();

            $this->addFlash(
                'notice',
                'Brand Edited'
            );
            return $this->redirectToRoute('app_brand');
        }
        return $this->renderForm('brand/edit.html.twig', ['form' => $form, 'id' => $id]);
    }
}
