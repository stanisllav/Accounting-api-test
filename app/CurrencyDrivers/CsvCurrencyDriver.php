<?php

namespace App\CurrencyDrivers;

use Illuminate\Support\Facades\Storage;

class CsvCurrencyDriver extends CurrencyDriver
{
    public function fetchData(): array
    {

        $csv = Storage::get('currency/currency.csv');

        // Convert the CSV content to an array of lines
        $csvLines = explode("\n", $csv);
        $currencyData = [];

        // Flag to check if we are processing the header
        $isHeader = true;

        // Iterate over each line in the CSV
        foreach ($csvLines as $line) {
            // Parse the line into an array
            $data = str_getcsv($line);

            // Skip empty lines
            if (count($data) < 2) {
                continue;
            }

            // Check if we are processing the header
            if ($isHeader) {
                // Skip the header and set the flag to false
                $isHeader = false;

                continue;
            }

            // Assign values to the associative array using the header as keys
            $currencyData[$data[0]] = $data[1];
        }

        return $currencyData;
    }
}
