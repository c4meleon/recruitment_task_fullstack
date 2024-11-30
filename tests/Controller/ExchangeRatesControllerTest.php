<?php

declare(strict_types=1);

namespace Controller;

use App\Service\ExchangeRateServiceInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ExchangeRatesControllerTest extends WebTestCase
{
    private $client;
    private $exchangeRateService;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->exchangeRateService = $this->createMock(ExchangeRateServiceInterface::class);
    }

    public function testIndexWithValidDate()
    {
        $this->exchangeRateService->method('getExchangeRates')
            ->willReturn([
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
            ]);

        $this->client->getContainer()->set('App\Service\CachedExchangeRateService', $this->exchangeRateService);

        $this->client->request('GET', '/api/exchange-rates?date=2023-09-15');

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $this->assertJson($response->getContent());
    }

    public function testIndexWithInvalidDateFormat()
    {
        $this->client->request('GET', '/api/exchange-rates?date=invalid-date');

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertStringContainsString('Invalid date format.', $response->getContent());
    }

    public function testIndexWithDateOutOfRange()
    {
        $this->client->request('GET', '/api/exchange-rates?date=2022-12-31');

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertStringContainsString('Date out of range.', $response->getContent());
    }

    public function testIndexWithServiceException()
    {
        $this->exchangeRateService->method('getExchangeRates')
            ->willThrowException(new \Exception('Service error'));

        $this->client->getContainer()->set('App\Service\CachedExchangeRateService', $this->exchangeRateService);

        $this->client->request('GET', '/api/exchange-rates?date=2023-09-15');

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $this->assertJson($response->getContent());
        $this->assertStringContainsString('Service error', $response->getContent());
    }
}
