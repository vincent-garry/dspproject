<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetUserPrizesController extends AbstractController
{
    public function __invoke(User $user): array
    {
        $prizes = [];
        foreach ($user->getCodes() as $code) {
            if ($code->isUsed()) {
                $prizes[] = [
                    'code' => $code->getCode(),
                    'prize' => $code->getPrize(),
                ];
            }
        }
        return $prizes;
    }
}