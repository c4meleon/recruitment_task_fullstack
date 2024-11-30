<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use App\Service\ExchangeRateServiceInterface;

class ExchangeRatesController extends AbstractController
{
    /**
     * @var ExchangeRateServiceInterface
     */
    private $exchangeRateService;

    public function __construct(ExchangeRateServiceInterface $exchangeRateService)
    {
        $this->exchangeRateService = $exchangeRateService;
    }

    public function index(Request $request)
    {
        $currencies = [
            'EUR' => 'Euro',
            'USD' => 'Dolar amerykański',
            'CZK' => 'Korona czeska',
            'IDR' => 'Rupia indonezyjska',
            'BRL' => 'Real brazylijski'
        ];

        $markups = [
            'EUR' => ['buy' => -0.05, 'sell' => 0.07],
            'USD' => ['buy' => -0.05, 'sell' => 0.07],
            'CZK' => ['buy' => null, 'sell' => 0.15],
            'IDR' => ['buy' => null, 'sell' => 0.15],
            'BRL' => ['buy' => null, 'sell' => 0.15],
        ];

        $selectedDate = $request->query->get('date', date('Y-m-d'));

        if (!$this->isValidISO8601Date($selectedDate)) {
            return new JsonResponse(['error' => 'Invalid date format.'], 400);
        }

        $startDate = new \DateTime('2023-01-01');
        $endDate = new \DateTime();
        $selectedDateTime = new \DateTime($selectedDate);

        if ($selectedDateTime < $startDate || $selectedDateTime > $endDate) {
            return new JsonResponse(['error' => 'Date out of range.'], 400);
        }

        try {
            $exchangeRates = $this->exchangeRateService->getExchangeRates($currencies, $markups, $selectedDate);
            return new JsonResponse($exchangeRates);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }

        return new JsonResponse($exchangeRates);
    }
    
    private function isValidISO8601Date(string $date): bool
    {
        $d = \DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}