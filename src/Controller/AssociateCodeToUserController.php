<?php

namespace App\Controller;

use App\Entity\Code;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Psr\Log\LoggerInterface;

class AssociateCodeToUserController extends AbstractController
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    #[Route('/codes/{code}/associate_user', methods: ['PATCH'], name: 'associate_code_to_user')]
    public function __invoke(string $code, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $this->logger->info('Controller invoked with code: ' . $code);

        $codeEntity = $entityManager->getRepository(Code::class)->findOneBy(['code' => $code]);

        if (!$codeEntity) {
            $this->logger->error('Code not found for code: ' . $code);
            throw new NotFoundHttpException('Code not found');
        }

        $data = json_decode($request->getContent(), true);
        $this->logger->info('Request data: ' . json_encode($data));
        $userEmail = $data['userEmail'] ?? null;

        if (!$userEmail) {
            $this->logger->error('User email is required');
            throw new BadRequestHttpException('User email is required');
        }

        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $userEmail]);

        if (!$user) {
            $this->logger->error('User not found for email: ' . $userEmail);
            throw new NotFoundHttpException('User not found');
        }

        $codeEntity->setUsers($user);
        $codeEntity->setIsUsed(true);
        $entityManager->flush();

        $this->logger->info('Code successfully associated with user');

        return new JsonResponse([
            'code' => $codeEntity->getCode(),
            'prize' => $codeEntity->getPrize(),
            'isUsed' => $codeEntity->getIsUsed(),
            'associatedUser' => $user->getEmail()
        ]);
    }
}
