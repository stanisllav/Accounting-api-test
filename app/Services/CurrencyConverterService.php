<?php

namespace App\Services;

use App\CurrencyDrivers\CurrencyDriverFactory;

class CurrencyConverterService
{
    public function convert(float $amount, string $from, string $to): float
    {

        $driver = CurrencyDriverFactory::create(config('currency.driver'));

        $exchangeRate = $driver->getExchangeRate($from, $to);

        return $amount * $exchangeRate;
    }
}
