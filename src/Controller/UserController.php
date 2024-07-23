<?php
namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    private UserRepository $userRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/api/users', name: 'get_users', methods: ['GET'])]
    public function getUsers(): JsonResponse
    {
        $users = $this->userRepository->findAll();

        $userData = [];
        foreach ($users as $user) {
            $userData[] = [
                'email' => $user->getEmail(),
                'codes' => array_map(function ($code) {
                    return [
                        'code' => $code->getCode(),
                        'prize' => $code->getPrize(),
                        'isUsed' => $code->isUsed(),
                    ];
                }, $user->getCodes()->toArray())
            ];
        }

        return new JsonResponse($userData);
    }

    #[Route('/api/users/{email}', name: 'fetch_user_by_email', methods: ['GET'])]
    public function fetchUserByEmail(string $email): JsonResponse
    {
        $user = $this->userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        return new JsonResponse([
            'email' => $user->getEmail(),
            'codes' => array_map(function ($code) {
                return [
                    'code' => $code->getCode(),
                    'prize' => $code->getPrize(),
                    'isUsed' => $code->isUsed(),
                ];
            }, $user->getCodes()->toArray())
        ]);
    }
}
