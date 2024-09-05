<?php

namespace App\Controller;

use App\Controller\Mail\BaseController;
use App\Repository\CodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LotteryController extends BaseController
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
            $this->addFlash('error_lottery', 'Code invalide ou déjà utilisé.');
            return $this->redirectToRoute('app_lottery');
        }

        $user = $this->getUser();
        if (!$user) {
            // Stocker le code dans la session et rediriger vers la page de connexion
            $request->getSession()->set('pending_code', $submittedCode);
            $this->addFlash('error_need_to_login', 'Code invalide ou déjà utilisé.');
            return $this->redirectToRoute('app_login');
        }

        $code->setUsers($user);
        $code->setUsed(true);
        $entityManager->flush();

        $mailContent = [
            'from' => new Address('noreply@thetiptop.com', 'No Reply'),
            'to' => $user->getEmail(),
            'subject' => 'Vous avez gagné un lot !',
            'htmlTemplate' => 'email/templates/code_validation_win.html.twig',
            'context' => [
                'prize' => [
                    'image' => $code->getImage(),
                    'name' => $code->getPrize(),
                    'price' => $code->getPrice(),
                    'description' => $code->getDescription(),
                ],
                'name' => $user->getFullName(),
                'mail' => $user->getEmail(),
                'claim_url' =>
                $this->generateUrl('app_my_prizes', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ]
        ];

        $this->MAILER->setMailContent($mailContent);

        try {
            $this->MAILER->send();
            $this->addFlash('success_lottery', 'Félicitations ! Vous avez gagné : ' . $code->getPrize());
        } catch (\Exception $e) {
            return $this->redirectToRoute('app_my_prizes');
        }
        return $this->redirectToRoute('app_my_prizes');
    }
}
