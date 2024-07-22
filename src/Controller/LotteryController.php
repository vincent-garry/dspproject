<?php

namespace App\Controller;

use App\Entity\Code;
use App\Repository\CodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LotteryController extends AbstractController
{
    #[Route('/lottery', name: 'app_lottery')]
    public function index(): Response
    {
        return $this->render('lottery/index.html.twig');
    }

    #[Route('/lottery/submit', name: 'app_lottery_submit', methods: ['POST'])]
    public function submit(Request $request, CodeRepository $codeRepository, EntityManagerInterface $entityManager): Response
    {
        $submittedCode = $request->request->get('code');
        $code = $codeRepository->findOneBy(['code' => $submittedCode, 'isUsed' => false]);

        if (!$code) {
            $this->addFlash('error', 'Code invalide ou déjà utilisé.');
            return $this->redirectToRoute('app_lottery');
        }

        $user = $this->getUser();
        if (!$user) {
            // Stocker le code dans la session et rediriger vers la page de connexion
            $request->getSession()->set('pending_code', $submittedCode);
            return $this->redirectToRoute('app_login');
        }

        $code->setUsers($user);
        $code->setUsed(true);
        $entityManager->flush();

        $this->addFlash('success', 'Félicitations ! Vous avez gagné : ' . $code->getPrize());
        return $this->redirectToRoute('app_my_prizes');
    }
}
