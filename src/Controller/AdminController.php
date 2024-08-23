<?php

namespace App\Controller;

use App\Entity\Code;
use App\Entity\User;
use App\Form\ValidatePriceType;
use App\Repository\CodeRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function dashboard(CodeRepository $ticketRepo, UserRepository $userRepo, CodeRepository $codeRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $form = $this->createForm(ValidatePriceType::class);
        $form->handleRequest($request);

        $genderStats = $userRepo->getGenderStats();

        $winner = null;
        if ($request->query->get('winner')) {
            $winner = $request->getSession()->get('lottery_winner');
            $request->getSession()->remove('lottery_winner'); // Nettoyer la session après utilisation
        } elseif ($winner = $userRepo->getBigwinner()) {
            $winner = $this->formatBigWinner($request, $winner);
        }

        $stats = [
            'totalTickets' => $ticketRepo->count([]),
            'usedTickets' => $ticketRepo->count(['isUsed' => true]),
            'totalPrizes' => $ticketRepo->count(['isUsed' => true]),
            'delivry' => $ticketRepo->count(['delivry' => true]),
            'notDelivered' => $ticketRepo->count(['isUsed' => true, 'delivry' => false]),
            'genderStats' => $this->formatStats($genderStats),
        ];

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user = $data['user'];
            $codeString = $data['code'];

            // Recherchez le code dans la base de données
            $code = $codeRepository->findOneBy(['code' => $codeString]);

            if ($code) {
                // Vérifiez si le code est associé à l'utilisateur sélectionné
                if ($this->isCodeAssociatedWithUser($code, $user)) {
                    // Marquez le code comme validé
                    $code->setDelivry(true);
                    $entityManager->persist($code);
                    $entityManager->flush();
                    $this->addFlash('success', 'Le code a été validé avec succès.');
                } else {
                    $this->addFlash('error', 'Ce code n\'est pas associé à l\'utilisateur sélectionné.');
                }
            } else {
                $this->addFlash('error', 'Code invalide ou inexistant dans la base de données.');
            }

            return $this->redirectToRoute('admin_dashboard');
        }

        return $this->render('admin/dashboard.html.twig', [
            'stats' => $stats,
            'winner' => $winner,
            'form' => $form->createView(),
        ]);
    }

    private function isCodeAssociatedWithUser($code, $user): bool
    {
        $associatedUsers = $code->getUsers();
        if ($associatedUsers == $user) {
            return true;
        }
        return false;
    }

    private function formatStats(array $stats): array
    {
        $formatted = [];
        foreach ($stats as $stat) {
            $key = $stat['gender'] ?? $stat['ageRange'] ?? 'Unknown';
            $formatted[$key] = $stat['count'];
        }
        return $formatted;
    }

    private function formatBigWinner(Request $request, User $bigWinner): array
    {
        $winner =  [
            'name' => $bigWinner->getFirstName() . ' ' . $bigWinner->getLastName(),
            'email' => $bigWinner->getEmail(),
            'id' => $bigWinner->getId()
        ];

        $request->getSession()->set('lottery_winner', $winner);

        return $winner;
    }

    #[Route('/email-data', name: 'admin_email_data')]
    public function emailData(UserRepository $userRepo): Response
    {
        $users = $userRepo->findAll();

        return $this->render('admin/email_data.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/tirage', name: 'admin_tirage')]
    public function tirage(Request $request, EntityManagerInterface $entityManager, CodeRepository $codeRepository, UserRepository $userRepository): Response
    {
        // Récupérer les utilisateurs uniques qui ont utilisé un code
        $users = $entityManager->createQuery(
            'SELECT DISTINCT u.id, u.firstName, u.lastName, u.email, u.bigwinner
         FROM App\Entity\User u
         JOIN App\Entity\Code c WITH c.users = u
         WHERE c.isUsed = :isUsed'
        )
            ->setParameter('isUsed', true)
            ->getResult();

        if (empty($users)) {
            $this->addFlash('warning', 'Aucun utilisateur éligible pour le tirage au sort.');
            return $this->redirectToRoute('admin_dashboard');
        } else {
            foreach ($users as $user) {
                if ($user['bigwinner'] == true) {
                    $this->addFlash('danger', 'Le tirage a déjà été effectué, le gagnant est ' . $user['firstName'] . ' ' . $user['lastName']);
                    return $this->redirectToRoute('admin_dashboard');
                }
            }
        }

        // Sélectionner un gagnant au hasard
        $randomIndex = array_rand($users);
        $winner = $users[$randomIndex];

        // Préparer les informations du gagnant
        $winnerInfo = [
            'name' => $winner['firstName'] . ' ' . $winner['lastName'],
            'email' => $winner['email'],
            'id' => $winner['id'],
        ];

        $user = $userRepository->findOneBy(['id' => $winner['id']]);
        $user->setBigwinner(true);
        $entityManager->persist($user);
        $entityManager->flush();

        // Stocker les informations du gagnant en session
        $request->getSession()->set('lottery_winner', $winnerInfo);

        // Ajouter un message flash
        $this->addFlash('success', 'Le tirage au sort a été effectué avec succès. Le gagnant est ' . $winnerInfo['name']);

        // Rediriger vers le dashboard admin
        return $this->redirectToRoute('admin_dashboard');
    }
}
