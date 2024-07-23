<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use App\Controller\AssociateCodeToUserController;
use App\Controller\UpdateCodeController;
use App\Repository\CodeRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;

#[ApiResource(
    operations: [
        new Get(),
        new GetCollection(),
        new Post(),
        new Put(),
        new Post(
            uriTemplate: '/codes/{code}/associate_user',
            controller: UpdateCodeController::class,
            uriVariables: [
                'code' => [
                    'type' => 'string',
                    'description' => 'The code to associate',
                    'required' => true,
                ],
            ],
            openapiContext: [
                'summary' => 'Associate a user with a code and mark the code as used',
                'description' => 'Associates a user with the code identified by {code}, and marks the code as used.',
                'requestBody' => [
                    'content' => [
                        'application/json' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'userEmail' => ['type' => 'string'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ),
    ]
)]
#[ORM\Entity(repositoryClass: CodeRepository::class)]
class Code
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ApiProperty(identifier: true)]
    #[ORM\Column(length: 255, unique: true)]
    private ?string $code = null;

    #[ORM\Column(length: 255)]
    private ?string $prize = null;

    #[ORM\ManyToOne(inversedBy: 'codes')]
    private ?User $users = null;

    #[ORM\Column]
    private ?bool $isUsed = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getPrize(): ?string
    {
        return $this->prize;
    }

    public function setPrize(string $prize): static
    {
        $this->prize = $prize;

        return $this;
    }

    public function getUsers(): ?User
    {
        return $this->users;
    }

    public function setUsers(?User $users): static
    {
        $this->users = $users;

        return $this;
    }

    public function isUsed(): ?bool
    {
        return $this->isUsed;
    }

    public function setUsed(bool $isUsed): static
    {
        $this->isUsed = $isUsed;

        return $this;
    }
}
