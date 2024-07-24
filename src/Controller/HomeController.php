<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        $prizes = [
            ['name' => 'Infuseur à thé', 'image' => 'infuseur.jpg', 'description' => 'Un infuseur à thé pratique', 'price' => "15,40", 'rank' => 1],
            ['name' => 'Boîte de thé détox', 'image' => 'détox.jpg', 'description' => '100g de thé détox', 'price' => "25,45", 'rank' => 2],
            ['name' => 'Boîte de thé signature', 'image' => 'signature.jpg', 'description' => '100g de thé signature', 'price' => "78,99", 'rank' => 3],
            ['name' => 'Coffret découverte', 'image' => 'coffret39.jpg', 'description' => 'Un assortiment de thés', 'price' => "39", 'rank' => 0],
            ['name' => 'Coffret découverte', 'image' => 'coffret69.jpg', 'description' => 'Notre meilleur assortiment', 'price' => "69", 'rank' => 0],
            // ['name' => 'Coffret découverte', 'image' => 'coffret69.jpg', 'description' => 'Notre meilleur assortiment', 'price' => "69", 'rank' => 0],
            // ['name' => 'Coffret découverte', 'image' => 'coffret69.jpg', 'description' => 'Notre meilleur assortiment', 'price' => "69", 'rank' => 0],
        ];

        return $this->render('home/index.html.twig', [
            'prizes' => $prizes,
        ]);
    }
}