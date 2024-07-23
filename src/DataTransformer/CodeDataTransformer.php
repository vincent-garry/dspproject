<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Dto\CodeDto;
use App\Entity\Code;

class CodeDataTransformer implements DataTransformerInterface
{
    public function transform($object, string $to, array $context = [])
    {
        $dto = new CodeDto();
        $dto->code = $object->getCode();
        $dto->prize = $object->getPrize();
        $dto->isUsed = $object->isUsed();
        $dto->userId = $object->getUsers() ? $object->getUsers()->getId() : null;
        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === CodeDto::class && $data instanceof Code;
    }
}