<?php

namespace App\CurrencyDrivers;

use Illuminate\Support\Facades\Storage;

class JsonCurrencyDriver extends CurrencyDriver
{
    public function fetchData(): array
    {
        $data = Storage::get('currency/currency.json');
        $ex = json_decode($data, true);
        // Accessing data
        $rates = [];
        foreach ($ex['exchangeRates'] as $item) {
            $rates[$item['currency']] = $item['rate'];
        }

        return $rates;
    }
}
