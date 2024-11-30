<?php

declare(strict_types=1);

namespace App\Service;

class ExchangeRateService implements ExchangeRateServiceInterface
{
    /**
     * @var ApiClientInterface
     */
    private $apiClient;

    /**
     * @var array
     */
    private $exchangeRates;

    public function __construct(ApiClientInterface $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @throws \Exception
     */
    public function getExchangeRates(array $currencies, array $markups, string $selectedDate): array
    {
        $this->exchangeRates = [];

        foreach ($currencies as $code => $name) {
            $this->exchangeRates[$code] = [
                'code' => $code,
                'name' => $name,
                'selected_date' => [
                    'nbp_rate' => 0,
                    'sell_rate' => 0,
                    'buy_rate' => null,
                ],
                'today' => [
                    'nbp_rate' => 0,
                    'sell_rate' => 0,
                    'buy_rate' => null,
                ],
            ];
        }

        try {
            $dataSelectedDate = $this->apiClient->fetchData("https://api.nbp.pl/api/exchangerates/tables/A/{$selectedDate}/?format=json");
            $ratesSelectedDate = $dataSelectedDate[0]['rates'];
            $this->processRates($ratesSelectedDate, $currencies, $markups, 'selected_date');

            $dataToday = $this->apiClient->fetchData('https://api.nbp.pl/api/exchangerates/tables/A/?format=json');
            $ratesToday = $dataToday[0]['rates'];
            $this->processRates($ratesToday, $currencies, $markups, 'today');
        } catch (\Exception $e) {
            throw new \Exception("Failed to fetch exchange rates: " . $e->getMessage());
        }

        return $this->exchangeRates;
    }

    private function processRates(array $rates, array $currencies, array $markups, string $dateType): void
    {
        foreach ($rates as $rate) {
            if (array_key_exists($rate['code'], $currencies)) {
                $nbpRate = round($rate['mid'], 4);
                $sellRate = round($nbpRate + $markups[$rate['code']]['sell'], 4);
                $buyRate = $markups[$rate['code']]['buy'] !== null ? round($nbpRate + $markups[$rate['code']]['buy'], 4) : null;

                $this->exchangeRates[$rate['code']][$dateType] = [
                    'nbp_rate' => $nbpRate,
                    'sell_rate' => $sellRate,
                    'buy_rate' => $buyRate,
                ];
            }
        }
    }
}
