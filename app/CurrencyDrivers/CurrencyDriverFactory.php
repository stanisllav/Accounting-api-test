<?php

namespace App\CurrencyDrivers;

use InvalidArgumentException;

class CurrencyDriverFactory
{
    public static function create($driver): CurrencyDriver
    {
        return match ($driver) {
            'xml' => new XmlCurrencyDriver(),
            'json' => new JsonCurrencyDriver(),
            'csv' => new CsvCurrencyDriver(),
            'average' => new AverageCurrencyDriver([new XmlCurrencyDriver(), new JsonCurrencyDriver(), new CsvCurrencyDriver()]),
            default => throw new InvalidArgumentException('Invalid currency driver'),
        };
    }
}
