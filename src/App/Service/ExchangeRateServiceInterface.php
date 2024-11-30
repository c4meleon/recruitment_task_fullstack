<?php

declare(strict_types=1);

namespace App\Service;

interface ExchangeRateServiceInterface
{
    public function getExchangeRates(array $currencies, array $markups, string $selectedDate): array;
}
