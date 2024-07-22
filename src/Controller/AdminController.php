<?php

namespace App\Controller;

use App\Repository\CodeRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function dashboard(CodeRepository $ticketRepo, UserRepository $userRepo): Response
    {
        $stats = [
            'totalTickets' => $ticketRepo->count([]),
            'usedTickets' => $ticketRepo->count(['isUsed' => true]),
            'totalPrizes' => $ticketRepo->count(['isUsed' => true]),
            'genderStats' => $userRepo->getGenderStats(),
        ];

        return $this->render('admin/dashboard.html.twig', [
            'stats' => $stats,
        ]);
    }

    #[Route('/email-data', name: 'admin_email_data')]
    public function emailData(UserRepository $userRepo): Response
    {
        $users = $userRepo->findAll();

        return $this->render('admin/email_data.html.twig', [
            'users' => $users,
        ]);
    }
}
