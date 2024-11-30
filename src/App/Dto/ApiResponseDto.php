<?php

declare(strict_types=1);

namespace App\Dto;

class ApiResponseDto
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $value;

    public function __construct(string $name, int $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): int
    {
        return $this->value;
    }
}
