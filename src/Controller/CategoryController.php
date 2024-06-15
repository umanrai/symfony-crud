<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/category')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'app_category_index', methods: ['GET'])]
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFile = $form->get('categoryImage')->getData();

            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile);
                $category->setImageFilename($imageFileName);
            } else {
                $category->setImageFilename('default_image.jpg');
            }

            $entityManager->persist($category);
            $entityManager->flush();
            $this->addFlash('success', 'Category created successfully!');

            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category/new.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category, EntityManagerInterface $entityManager, FileUploader $fileUploader): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('categoryImage')->getData();

            if ($imageFile) {
                $oldImageName = $category->getImageFilename();

                $imageFileName = $fileUploader->upload($imageFile);
                $category->setImageFilename($imageFileName);

                if ($oldImageName && $oldImageName !== 'default_image.jpg'){
                    // Define the path to the old image file
                    $oldImageFilePath = $this->getParameter('image_directory') . '/' . $oldImageName;

                    // Remove the old image file
                    if (file_exists($oldImageFilePath)) {
                        unlink($oldImageFilePath);
                    }
                } else {
                    $image = $category->getImageFilename();
                    $category->setImageFilename($image);
                }

            }

            $entityManager->flush();
            $this->addFlash('success', 'Category updated successfully!');

            return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category, EntityManagerInterface $entityManager): Response
    {

        $oldCategoryImageName = $category->getImageFilename();

        if ($oldCategoryImageName && $oldCategoryImageName !== 'default_image.jpg') {
            // Define the path to the old image file
            $oldImageFilePath = $this->getParameter('image_directory') . '/' . $oldCategoryImageName;

            // Remove the old image file
            if (file_exists($oldImageFilePath)) {
                unlink($oldImageFilePath);
            }

            $entityManager->flush();
        }

        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($category);
            $entityManager->flush();
        }

        $this->addFlash('success', 'Category deleted successfully!');
        return $this->redirectToRoute('app_category_index', [], Response::HTTP_SEE_OTHER);
    }
}
