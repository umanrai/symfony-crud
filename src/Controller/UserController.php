<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/user')]
class UserController extends AbstractController
{

    #[Route('/', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
                'users' => $userRepository->findAll()
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, FileUploader $fileUploader, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $password = $form->get('password')->getData();

            $user->setPassword($userPasswordHasher->hashPassword($user, $password));

            $imageFile = $form->get('userImage')->getData();

            if ($imageFile) {
                $imageFileName = $fileUploader->upload($imageFile);
                $user->setImageFilename($imageFileName);
            } else {
                $user->setImageFilename('default_image.jpg');
            }

            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'User created successfully!');

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager,  FileUploader $fileUploader, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();

            $user->setPassword($userPasswordHasher->hashPassword($user, $password));


            $imageFile = $form->get('userImage')->getData();

            if ($imageFile) {
                $oldImageName = $user->getImageFilename();

                $imageFileName = $fileUploader->upload($imageFile);
                $user->setImageFilename($imageFileName);

                if ($oldImageName && $oldImageName !== 'default_image.jpg') {
                    // Define the path to the old image file
                    $oldImageFilePath = $this->getParameter('image_directory') . '/' . $oldImageName;

                    // Remove the old image file
                    if (file_exists($oldImageFilePath)) {
                        unlink($oldImageFilePath);
                    }
                }

            } else {
                $image = $user->getImageFilename();
                $user->setImageFilename($image);
            }

            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'User updated successfully!');

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {

        $oldUserImageName = $user->getImageFilename();

        if ($oldUserImageName && $oldUserImageName !== 'default_image.jpg') {
            // Define the path to the old image file
            $oldImageFilePath = $this->getParameter('image_directory') . '/' . $oldUserImageName;

            // Remove the old image file
            if (file_exists($oldImageFilePath)) {
                unlink($oldImageFilePath);
            }

            $entityManager->flush();
        }

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        $this->addFlash('success', 'User deleted successfully!');
        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

}