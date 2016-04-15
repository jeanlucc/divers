<?php
$currenciesRates = [];
for ($i = 0; $i < 1000000; $i++) {
    $currenciesRates[] = $i;
}

function fref($currenciesRates) {
    $toto = [];
    foreach ($currenciesRates as &$exchangeRate) {
        $toto[] = $exchangeRate;
    }
};

function fcopy($currenciesRates) {
    $toto = [];
    foreach ($currenciesRates as $exchangeRate) {
        $toto[] = $exchangeRate;
    }
};

$start = microtime(true);
$ref = fref($currenciesRates);
$stop = microtime(true);
$time_ref = $stop - $start;

$start = microtime(true);
$copy = fcopy($currenciesRates);
$stop = microtime(true);
$time_copy = $stop - $start;

echo 'time ref : ' . $time_ref  . "\tresult :" . $ref  . "\n";
echo 'time copy: ' . $time_copy . "\tresult :" . $copy . "\n";
