<?php

namespace App\Controller;

use App\Repository\CodeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyPrizesController extends AbstractController
{
    #[Route('/my-prizes', name: 'app_my_prizes')]
    public function index(CodeRepository $codeRepository, Security $security): Response
    {
        $user = $security->getUser();
        $prizes = $codeRepository->findBy(['users' => $user, 'isUsed' => true]);

        return $this->render('my_prizes/index.html.twig', [
            'prizes' => $prizes,
        ]);
    }
}