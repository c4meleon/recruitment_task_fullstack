<?php

declare(strict_types=1);

namespace App\Mapper;

use App\Dto\ApiResponseDto;

class ApiResponseMapper
{
    /**
     * @param array $data
     * @return ApiResponseDto
     */
    public function map(array $data): ApiResponseDto
    {
        return new ApiResponseDto($data['name'], $data['value']);
    }
}
