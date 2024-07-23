<?php

namespace App\Controller;

use App\Entity\Code;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[AsController]
class AssociateCodeToUserController extends AbstractController
{
    public function __invoke(Request $request, Code $code, EntityManagerInterface $entityManager): Code
    {
        $userId = $request->request->get('userId');
        if (!$userId) {
            throw new BadRequestHttpException('User ID is required');
        }

        $user = $entityManager->getRepository(User::class)->find($userId);
        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        $code->setUsers($user);
        $entityManager->flush();

        return $code;
    }
}