<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CachedExchangeRateService extends ExchangeRateService
{
    private $cache;

    public function __construct(ApiClientInterface $apiClient, CacheInterface $cache)
    {
        parent::__construct($apiClient);
        $this->cache = $cache;
    }

    /**
     * @throws \Psr\Cache\InvalidArgumentException
     */
    public function getExchangeRates(array $currencies, array $markups, string $selectedDate): array
    {
        $cacheKey = 'exchange_rates_' . $selectedDate;

        return $this->cache->get($cacheKey, function (ItemInterface $item) use ($currencies, $markups, $selectedDate) {
            $item->expiresAt((new \DateTime())->setTime(23, 59, 59));
            return parent::getExchangeRates($currencies, $markups, $selectedDate);
        });
    }
}