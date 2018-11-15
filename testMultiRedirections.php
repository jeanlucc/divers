<?php
$patterns = ['toto', 'ta*', 'tititi', 'tata*'];
$destinations = ['tutu', 'tata', 'titi'];

$repeatedPatterns = [];
$repeatedDestinations = [];
for ($i = 0; $i < 1000; $i++) {
    foreach ($patterns as $pattern) {
        $repeatedPatterns[] = $pattern;
    }
    foreach ($destinations as $destination) {
        $repeatedDestinations[] = $destination.$i;
    }
}

function testPatternsOnPatterns($patterns)
{
    if (2 > count($patterns)) {
        return [];
    }
    $uselessPatterns = [];
    for ($i = 0; $i < count($patterns); $i++) {
        $pattern = $patterns[$i];
        if (strlen($pattern) - 1 === strrpos($pattern, '*')) {
            $pattern = substr($pattern, 0, -1).'.*';
        }
        $pattern = '/^'.$pattern.'$/';
        for ($j = $i + 1; $j < count($patterns); $j++) {
            $testedPattern = $patterns[$j];
            if (0 < preg_match($pattern, $testedPattern)) {
                $uselessPatterns[] = array_splice($patterns, $j, 1)[0];
            }
        }
    }

    return $uselessPatterns;
}

function testPatternsOnDestinations($patterns, $destinations)
{
    $redirectedDestinations = [];
    foreach ($patterns as $pattern) {
        if (strlen($pattern) - 1 === strrpos($pattern, '*')) {
            $pattern = substr($pattern, 0, -1).'.*';
        }
        $pattern = '/^'.$pattern.'$/';
        foreach ($destinations as $destination) {
            if (0 < preg_match($pattern, $destination)) {
                $redirectedDestinations[] = $destination;
            }
        }
    }

    return $redirectedDestinations;
}

$start = microtime(true);
$redirectedDestinations = testPatternsOnDestinations($repeatedPatterns, $repeatedDestinations);
//$uselessPatterns = testPatternsOnPatterns($repeatedPatterns);
$stop = microtime(true);
$time = $stop - $start;

echo 'time: '.$time."\n";
//var_dump($redirectedDestinations);
//var_dump($uselessPatterns);