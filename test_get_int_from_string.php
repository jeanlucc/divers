<?php

// Note that its not a strict comparison since all results are not the same

function f_intval ($values) {
    $data = [];
    foreach ($values as $value) {
        $data[] = intval($value);
    }
    return $data;
}

function f_cast_int ($values) {
    $data = [];
    foreach ($values as $value) {
        $data[] = (int) $value;
    }
    return $data;
}

function f_intval_with_preg_replace ($values) {
    $data = [];
    foreach ($values as $value) {
        $data[] = intval(preg_replace('/^[^0-9]*/', '', $value));
    }
    return $data;
}

function f_cast_int_with_preg_replace ($values) {
    $data = [];
    foreach ($values as $value) {
        $data[] = (int) preg_replace('/^[^0-9]*/', '', $value);
    }
    return $data;
}

function f_filter_var ($values) {
    $data = [];
    foreach ($values as $value) {
        $data[] = filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }
    return $data;
}

function f_preg_replace_remove ($values) {
    $data = [];
    foreach ($values as $value) {
        $data[] = preg_replace('/[^0-9]/','',$value);
    }
    return $data;
}

function f_preg_replace_keep ($values) {
    $data = [];
    foreach ($values as $value) {
        $data[] = preg_replace('/^[^0-9]*([0-9]+).*$/','${1}',$value);
    }
    return $data;
}

function f_preg_match_one ($values) {
    $data = [];
    foreach ($values as $value) {
        preg_match_all('/\d+/', $value, $matches);
        $data[] = $matches[0][0];
    }
    return $data;
}

function f_preg_match_all ($values) {
    $data = [];
    foreach ($values as $value) {
        preg_match_all('/\d+/', $value, $matches);
        foreach ($matches[0] as $match) {
            $data[] = $match;
        }
    }
    return $data;
}

$values = [];
for ($i = 0; $i<50000; ++$i) {
    $values[] = md5(rand(0,10000));
}

$functions = ['f_intval', 'f_cast_int', 'f_intval_with_preg_replace', 'f_cast_int_with_preg_replace', 'f_filter_var', 'f_preg_replace_remove', 'f_preg_replace_keep', 'f_preg_match_one', 'f_preg_match_all'];
$results = [];
$checks = [];

foreach($functions as $func) {
    $start = microtime(true);
    $func($values);
    $results[$func] = (microtime(true) - $start) * 1000;
//    $checks[$func] = $func($values);
}

asort($results);

foreach($results as $func => $time) {
    echo "$func: " . number_format($time, 1) . " ms\n";
}

//var_dump($checks);
