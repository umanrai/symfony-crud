<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Service\FileUploader;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


#[Route('/admin/product')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll()
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile);
                $product->setImageFilename($imageFileName);
            } else {
                $product->setImageFilename('default_image.jpg');
            }


//            if ($image) {
//                $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
//                $safeFilename = $slugger->slug($originalFilename);
//                $newFilename = $safeFilename.'-'.uniqid().'.'.$image->guessExtension();
//
//                try {
//                    $image->move($imageDirectory, $newFilename);
//                } catch (FileException $e) {
//                    //
//                }
//                $product->setImageFilename($newFilename);
//            }


            $entityManager->persist($product);
            $entityManager->flush();

            $this->addFlash('success', 'Product saved successfully!');
            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager,  FileUploader $fileUploader): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $oldImageName = $product->getImageFilename();

                $imageFileName = $fileUploader->upload($imageFile);
                $product->setImageFilename($imageFileName);

                if ($oldImageName && $oldImageName !== 'default_image.jpg') {
                    // Define the path to the old image file
                    $oldImageFilePath = $this->getParameter('image_directory') . '/' . $oldImageName;

                    // Remove the old image file
                    if (file_exists($oldImageFilePath)) {
                        unlink($oldImageFilePath);
                    }
                }

            } else {
                $image = $product->getImageFilename();
                $product->setImageFilename($image);
            }

            $entityManager->flush();
            $this->addFlash('success', 'Product updated successfully!');

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $oldProductImageName = $product->getImageFilename();

        if ($oldProductImageName && $oldProductImageName !== 'default_image.jpg') {
            // Define the path to the old image file
            $oldImageFilePath = $this->getParameter('image_directory') . '/' . $oldProductImageName;

            // Remove the old image file
            if (file_exists($oldImageFilePath)) {
                unlink($oldImageFilePath);
            }
        }

        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        $this->addFlash('success', 'Product deleted successfully!');
        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }

}
