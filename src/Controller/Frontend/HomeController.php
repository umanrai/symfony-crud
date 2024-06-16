<?php

namespace App\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/')]
class HomeController extends AbstractController
{

    #[Route('/', name: 'frontend_home', methods: ['GET'])]
    public function index()
    {
        return $this->render('frontend/home/index.html.twig');
    }

}