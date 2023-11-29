<?php

namespace App\CurrencyDrivers;

use App\CurrencyDrivers\CurrencyDriver;

class AverageCurrencyDriver extends CurrencyDriver
{

    private array $drivers;

    public function __construct(array $drivers)
    {
        $this->drivers = $drivers;
    }

    public function fetchData(): array
    {

// Use array_map to fetch data and sum values for each key
        $dataArrays = array_map(function (CurrencyDriver $driver) {
            return $driver->fetchData();
        }, $this->drivers);

// Calculate the average for each key
        $numObjects = count($this->drivers);

// Initialize an associative array for the averages
        $averages = [];

// Iterate over the keys and calculate the average for each key
        foreach ($dataArrays[0] as $key => $value) {
            $sum = array_sum(array_column($dataArrays, $key));
            $averages[$key] = $sum / $numObjects;
        }

        return $averages;
    }

}
