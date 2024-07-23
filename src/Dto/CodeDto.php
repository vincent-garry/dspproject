<?php

namespace App\Dto;

class CodeDto
{
    public ?string $code = null;
    public ?string $prize = null;
    public ?bool $isUsed = null;
    public ?int $userId = null;
}