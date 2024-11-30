<?php

declare(strict_types=1);

namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;
use App\Service\CachedExchangeRateService;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use App\Service\ApiClientInterface;

class CachedExchangeRateServiceTest extends TestCase
{
    private $cache;
    private $cachedExchangeRateService;

    protected function setUp(): void
    {
        $apiClient = $this->createMock(ApiClientInterface::class);
        $this->cache = $this->createMock(CacheInterface::class);

        $this->cachedExchangeRateService = new CachedExchangeRateService($apiClient, $this->cache);
    }

    public function testGetExchangeRatesFromCache()
    {
        $currencies = ['EUR' => 'Euro'];
        $markups = ['EUR' => ['buy' => -0.05, 'sell' => 0.07]];
        $selectedDate = '2023-09-15';

        $cachedData = [
            'EUR' => [
                'code' => 'EUR',
                'name' => 'Euro',
                'selected_date' => [
                    'nbp_rate' => 4.5,
                    'sell_rate' => 4.57,
                    'buy_rate' => 4.45,
                ],
                'today' => [
                    'nbp_rate' => 4.6,
                    'sell_rate' => 4.67,
                    'buy_rate' => 4.55,
                ],
            ],
        ];

        $this->cache->method('get')
            ->with('exchange_rates_' . $selectedDate)
            ->willReturn($cachedData);

        $result = $this->cachedExchangeRateService->getExchangeRates($currencies, $markups, $selectedDate);

        $this->assertEquals($cachedData, $result);
    }
}