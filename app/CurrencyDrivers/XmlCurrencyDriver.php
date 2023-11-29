<?php

namespace App\CurrencyDrivers;

use App\CurrencyDrivers\CurrencyDriver;
use Illuminate\Support\Facades\Storage;

class XmlCurrencyDriver extends CurrencyDriver
{

    public function fetchData(): array
    {
        $xml = Storage::get('currency/currency.xml');

        // Load the XML string
        $xml = simplexml_load_string($xml);

        // Convert the XML object to an array
        $json = json_encode($xml);
        $currencyData = json_decode($json, true);


        $rates = [];

        foreach ($currencyData['currency'] as $currency) {
            $rates[$currency['code']] = $currency['rate'];
        }


        return $rates;
    }

}
