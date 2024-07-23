<?php

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\Dto\UserDto;
use App\Entity\User;

class UserDataTransformer implements DataTransformerInterface
{
    public function transform($object, string $to, array $context = [])
    {
        $dto = new UserDto();
        $dto->email = $object->getEmail();
        $dto->firstName = $object->getFirstName();
        $dto->lastName = $object->getLastName();
        $dto->gender = $object->getGender();
        $dto->birthdate = $object->getBirthdate();
        $dto->address = $object->getAddress();
        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === UserDto::class && $data instanceof User;
    }
}