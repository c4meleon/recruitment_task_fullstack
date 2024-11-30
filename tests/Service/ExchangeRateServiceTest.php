<?php

declare(strict_types=1);

namespace Service;

use PHPUnit\Framework\TestCase;
use App\Service\ExchangeRateService;
use App\Service\ApiClientInterface;

class ExchangeRateServiceTest extends TestCase
{
    private $apiClient;
    private $exchangeRateService;

    protected function setUp(): void
    {
        $this->apiClient = $this->createMock(ApiClientInterface::class);
        $this->exchangeRateService = new ExchangeRateService($this->apiClient);
    }

    public function testGetExchangeRates()
    {
        $currencies = ['EUR' => 'Euro'];
        $markups = ['EUR' => ['buy' => -0.05, 'sell' => 0.07]];
        $selectedDate = '2023-09-15';

        $dataSelectedDate = [
            [
                'rates' => [
                    [
                        'code' => 'EUR',
                        'mid' => 4.5,
                    ],
                ],
            ],
        ];

        $dataToday = [
            [
                'rates' => [
                    [
                        'code' => 'EUR',
                        'mid' => 4.6,
                    ],
                ],
            ],
        ];

        $this->apiClient->method('fetchData')
            ->willReturnOnConsecutiveCalls($dataSelectedDate, $dataToday);

        $result = $this->exchangeRateService->getExchangeRates($currencies, $markups, $selectedDate);

        $expected = [
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

        $this->assertEquals($expected, $result);
    }

    public function testGetExchangeRatesWithException()
    {
        $currencies = ['EUR' => 'Euro'];
        $markups = ['EUR' => ['buy' => -0.05, 'sell' => 0.07]];
        $selectedDate = '2023-09-15';

        $this->apiClient->method('fetchData')
            ->willThrowException(new \Exception('API error'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to fetch exchange rates: API error');

        $this->exchangeRateService->getExchangeRates($currencies, $markups, $selectedDate);
    }
}
