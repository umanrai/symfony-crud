<?php

namespace App\Controller\Frontend;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/')]
class HomeController extends AbstractController
{

    public function __construct(
        private ProductRepository $product
    )
    {
    }

    #[Route('/', name: 'frontend_home', methods: ['GET'])]
    public function index()
    {
//        dd($this->product->findLatestProducts());
        return $this->render('frontend/home/index.html.twig', [
            'products' => $this->product->findLatestProducts(),
            'productListTitle' => "Featured Products"
        ]);
    }

}