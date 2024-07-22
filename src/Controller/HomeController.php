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
            ['name' => 'Infuseur à thé', 'image' => 'infuseur.jpg', 'description' => 'Un infuseur à thé pratique'],
            ['name' => 'Boîte de thé détox', 'image' => 'detox.jpg', 'description' => '100g de thé détox'],
            ['name' => 'Boîte de thé signature', 'image' => 'signature.jpg', 'description' => '100g de thé signature'],
            ['name' => 'Coffret découverte 39€', 'image' => 'coffret39.jpg', 'description' => 'Un assortiment de thés'],
            ['name' => 'Coffret découverte 69€', 'image' => 'coffret69.jpg', 'description' => 'Notre meilleur assortiment'],
        ];

        return $this->render('home/index.html.twig', [
            'prizes' => $prizes,
        ]);
    }
}