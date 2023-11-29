<?php


use App\Facades\CurrencyConverter;

function convertCurrency($amount, $from, $to)
{
    return CurrencyConverter::convert($amount, $from, $to);
}
