<?php

namespace App\Controller;

use App\Entity\Code;
use App\Entity\User;
use App\Repository\CodeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class CodeController extends AbstractController
{
    private CodeRepository $codeRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(CodeRepository $codeRepository, EntityManagerInterface $entityManager)
    {
        $this->codeRepository = $codeRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/api/codes', name: 'get_all_codes', methods: ['GET'])]
    public function getAllCodes(): JsonResponse
    {
        // Récupérer tous les codes
        $codes = $this->codeRepository->findAll();

        // Préparer les données pour la réponse
        $codeData = array_map(function (Code $code) {
            return [
                'code' => $code->getCode(),
                'prize' => $code->getPrize(),
                'isUsed' => $code->isUsed(),
            ];
        }, $codes);

        return new JsonResponse($codeData);
    }

    #[Route('/api/codes/{code}', name: 'get_code', methods: ['GET'])]
    public function getCode(string $code): JsonResponse
    {
        $codeEntity = $this->codeRepository->findOneBy(['code' => $code]);

        if (!$codeEntity) {
            throw new NotFoundHttpException('Code not found');
        }

        return new JsonResponse([
            'code' => $codeEntity->getCode(),
            'prize' => $codeEntity->getPrize(),
            'isUsed' => $codeEntity->isUsed(),
        ]);
    }

    #[Route('/api/codes/{code}/associate_user', name: 'associate_code_to_user', methods: ['PATCH'])]
    public function associateUser(string $code, Request $request): JsonResponse
    {
        $codeEntity = $this->codeRepository->findOneBy(['code' => $code]);

        if (!$codeEntity) {
            throw new NotFoundHttpException('Code not found');
        }

        $data = json_decode($request->getContent(), true);
        $userEmail = $data['userEmail'] ?? null;

        if (!$userEmail) {
            throw new BadRequestHttpException('User email is required');
        }

        // Assume User is another entity and we can find it by email
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $userEmail]);

        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        $codeEntity->setUsers($user);
        $codeEntity->setUsed(true);
        $this->entityManager->flush();

        return new JsonResponse([
            'code' => $codeEntity->getCode(),
            'prize' => $codeEntity->getPrize(),
            'isUsed' => $codeEntity->isUsed(),
            'associatedUser' => $user->getEmail(),
        ]);
    }
    #[Route('/api/codes/{code}/delivry', name: 'delivry_code_to_user', methods: ['PATCH'])]
    public function delivryCode(string $code, Request $request): JsonResponse
    {
        $codeEntity = $this->codeRepository->findOneBy(['code' => $code]);

        if (!$codeEntity) {
            throw new NotFoundHttpException('Code not found');
        }

        if($codeEntity->getUsers()->getFirstName()){
            $codeEntity->setDelivry(true);
            $codeEntity->setUsed(true);
            $this->entityManager->flush();
        } else{
            throw new BadRequestHttpException('Pas d\'utilisateur lié');
        }

        return new JsonResponse([
            'Modifié avec succès'
        ]);
    }
}
