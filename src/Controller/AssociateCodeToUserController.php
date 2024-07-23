<?php

namespace App\Controller;

use App\Entity\Code;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

#[AsController]
class AssociateCodeToUserController extends AbstractController
{
    public function __invoke(string $code, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $codeEntity = $entityManager->getRepository(Code::class)->findOneBy(['code' => $code]);

        if (!$codeEntity) {
            throw new NotFoundHttpException('Code not found');
        }

        $data = json_decode($request->getContent(), true);
        $userEmail = $data['userEmail'] ?? null;

        if (!$userEmail) {
            throw new BadRequestHttpException('User email is required');
        }

        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $userEmail]);

        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        $codeEntity->setUsers($user);
        $codeEntity->setUsed(true);
        $entityManager->flush();

        return new JsonResponse([
            'code' => $codeEntity->getCode(),
            'prize' => $codeEntity->getPrize(),
            'isUsed' => $codeEntity->isUsed(),
            'associatedUser' => $user->getEmail()
        ]);
    }
}