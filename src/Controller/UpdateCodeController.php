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

class UpdateCodeController extends AbstractController
{
    #[Route('/codes/{code}/associate_user', name: 'associate_code_to_user', methods: ['PATCH'])]
    public function __invoke(string $code, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // Rechercher le code par son identifiant
        $codeEntity = $entityManager->getRepository(Code::class)->findOneBy(['code' => $code]);

        if (!$codeEntity) {
            throw new NotFoundHttpException('Code not found');
        }

        // Récupérer les données de la requête
        $data = json_decode($request->getContent(), true);
        $userEmail = $data['userEmail'] ?? null;

        if (!$userEmail) {
            throw new BadRequestHttpException('User email is required');
        }

        // Rechercher l'utilisateur par son adresse e-mail
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => 'vgarry7@gmail.com']);

        dd($user);

        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        // Associer l'utilisateur au code et marquer le code comme utilisé
        $codeEntity->setUsers($user);
        $codeEntity->setIsUsed(true);

        // Sauvegarder les modifications
        $entityManager->flush();

        // Réponse
        return new JsonResponse([
            'code' => $codeEntity->getCode(),
            'prize' => $codeEntity->getPrize(),
            'isUsed' => $codeEntity->isUsed(),
            'associatedUser' => $user->getEmail(),
        ]);
    }
}
