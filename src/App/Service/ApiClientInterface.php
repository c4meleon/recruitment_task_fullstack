<?php

declare(strict_types=1);

namespace App\Service;

interface ApiClientInterface
{
    public function fetchData(string $url): array;
}
