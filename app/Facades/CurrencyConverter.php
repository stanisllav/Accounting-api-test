<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class CurrencyConverter extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'currencyConverter';
    }
}
