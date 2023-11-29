<?php

namespace App\CurrencyDrivers;

use Illuminate\Support\Facades\Cache;

abstract class CurrencyDriver
{

    private $supportedCurrencies = ['USD', 'EUR'];

    final public function getExchangeRate(string $from = "EUR", string $to = "USD"): float
    {

        if (!in_array($from, $this->supportedCurrencies)) {
            throw new \InvalidArgumentException("currency: $from not supported");
        }
        if (!in_array($to, $this->supportedCurrencies)) {
            throw new \InvalidArgumentException("currency: $to not supported");
        }
        //
        if ($from === $to)
            return 1;


        $currencyKey = $from . "_" . $to;

        $cacheKey = $this->getCacheKey($currencyKey);

        // Check if the exchange rate is already cached
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey)[$currencyKey];
        }

        // If not cached, fetch data and calculate exchange rate
        $exchangeRates = $this->fetchData();

        // Cache the exchange rate with a specified expiration time
        $this->updateCache($exchangeRates);

        return $exchangeRates[$currencyKey];
    }

    protected function getCacheKey(): string
    {
        // Generate a unique cache key based on the driver class and any specific parameters
        return 'currency:' . static::class;
    }


    public function clearCache(): void
    {
        Cache::forget($this->getCacheKey());
    }

    abstract public function fetchData(): array;

    public function updateCache(array $exchangeRates): void
    {

        $this->clearCache();
        Cache::put($this->getCacheKey(), $exchangeRates, now()->addMinutes(config('currency.cache_duration')));

    }

}
