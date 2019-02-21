<?php

/**
 * One output on a particular setup if you want very partial view quickly
 *
 * timeWithNullLoggerSetForCreationAndExecution: 1.4389019012451
 * timeWithNullLoggerUnsetForCreationAndExecution: 1.5223889350891
 * timeWithNullLoggerSetForExecution: 0.90553283691406
 * timeWithNullLoggerUnsetForExecution: 0.89365911483765
 * timeWithNullSetForCreationAndExecution: 1.4972720146179
 * timeWithNullUnsetForCreationAndExecution: 0.68407392501831
 * timeWithNullSetForExecution: 1.0043950080872
 * timeWithNullUnsetForExecution: 0.2374050617218
 *
 * FOR SET LOGGER
 * timeWithNullLoggerSetForCreationAndExecution: 1.4389019012451
 * timeWithNullLoggerSetForExecution: 0.90553283691406
 * timeWithNullSetForCreationAndExecution: 1.4972720146179
 * timeWithNullSetForExecution: 1.0043950080872
 *
 * timeWithNullLoggerSetForCreationAndExecution: 1.4389019012451
 * timeWithNullSetForCreationAndExecution: 1.4972720146179
 * timeWithNullLoggerSetForExecution: 0.90553283691406
 * timeWithNullSetForExecution: 1.0043950080872
 */

require __DIR__.'/vendor/autoload.php';

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class WithNull
{
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    public function doSomething()
    {
        if ($this->logger) {
            $this->logger->debug('we are doing something.');
        }

        $a = 1;
        $b = 1;
        $c = $a + $b;

        if ($this->logger) {
            $this->logger->debug('we have done something.');
        }

        return $c;
    }
}

class WithNullLogger
{
    public function __construct(LoggerInterface $logger = null)
    {
        $this->logger = $logger ?: new NullLogger;
    }

    public function doSomething()
    {
        $this->logger->debug('we are doing something.');

        $a = 1;
        $b = 1;
        $c = $a + $b;

        $this->logger->debug('we have done something.');

        return $c;
    }
}

$logger = new NullLogger;
$iterations = 3000000;

$iterationsWithNullLoggerSetForCreationAndExecution = $iterations;
$iterationsWithNullLoggerUnsetForCreationAndExecution = $iterations;
$iterationsWithNullLoggerSetForExecution = $iterations;
$iterationsWithNullLoggerUnsetForExecution = $iterations;
$iterationsWithNullSetForCreationAndExecution = $iterations;
$iterationsWithNullUnsetForCreationAndExecution = $iterations;
$iterationsWithNullSetForExecution = $iterations;
$iterationsWithNullUnsetForExecution = $iterations;

$start = microtime(true);
for ($i=0; $i < $iterationsWithNullLoggerSetForCreationAndExecution; $i++) {
    $s = new WithNullLogger($logger);
    $s->doSomething();
}
$stop = microtime(true);
$timeWithNullLoggerSetForCreationAndExecution = $stop - $start;

$start = microtime(true);
for ($i=0; $i < $iterationsWithNullLoggerUnsetForCreationAndExecution; $i++) {
    $s = new WithNullLogger();
    $s->doSomething();
}
$stop = microtime(true);
$timeWithNullLoggerUnsetForCreationAndExecution = $stop - $start;

$s = new WithNullLogger($logger);
$start = microtime(true);
for ($i=0; $i < $iterationsWithNullLoggerSetForExecution; $i++) {
    $s->doSomething();
}
$stop = microtime(true);
$timeWithNullLoggerSetForExecution = $stop - $start;

$s = new WithNullLogger();
$start = microtime(true);
for ($i=0; $i < $iterationsWithNullLoggerUnsetForExecution; $i++) {
    $s->doSomething();
}
$stop = microtime(true);
$timeWithNullLoggerUnsetForExecution = $stop - $start;

$start = microtime(true);
for ($i=0; $i < $iterationsWithNullSetForCreationAndExecution; $i++) {
    $s = new WithNull($logger);
    $s->doSomething();
}
$stop = microtime(true);
$timeWithNullSetForCreationAndExecution = $stop - $start;

$start = microtime(true);
for ($i=0; $i < $iterationsWithNullUnsetForCreationAndExecution; $i++) {
    $s = new WithNull();
    $s->doSomething();
}
$stop = microtime(true);
$timeWithNullUnsetForCreationAndExecution = $stop - $start;

$s = new WithNull($logger);
$start = microtime(true);
for ($i=0; $i < $iterationsWithNullSetForExecution; $i++) {
    $s->doSomething();
}
$stop = microtime(true);
$timeWithNullSetForExecution = $stop - $start;

$s = new WithNull();
$start = microtime(true);
for ($i=0; $i < $iterationsWithNullUnsetForExecution; $i++) {
    $s->doSomething();
}
$stop = microtime(true);
$timeWithNullUnsetForExecution = $stop - $start;

if ($iterationsWithNullLoggerSetForCreationAndExecution > 0) {
    echo 'timeWithNullLoggerSetForCreationAndExecution: '.$timeWithNullLoggerSetForCreationAndExecution."\n";
}
if ($iterationsWithNullLoggerUnsetForCreationAndExecution > 0) {
    echo 'timeWithNullLoggerUnsetForCreationAndExecution: '.$timeWithNullLoggerUnsetForCreationAndExecution."\n";
}
if ($iterationsWithNullLoggerSetForExecution > 0) {
    echo 'timeWithNullLoggerSetForExecution: '.$timeWithNullLoggerSetForExecution."\n";
}
if ($iterationsWithNullLoggerUnsetForExecution > 0) {
    echo 'timeWithNullLoggerUnsetForExecution: '.$timeWithNullLoggerUnsetForExecution."\n";
}
if ($iterationsWithNullSetForCreationAndExecution > 0) {
    echo 'timeWithNullSetForCreationAndExecution: '.$timeWithNullSetForCreationAndExecution."\n";
}
if ($iterationsWithNullUnsetForCreationAndExecution > 0) {
    echo 'timeWithNullUnsetForCreationAndExecution: '.$timeWithNullUnsetForCreationAndExecution."\n";
}
if ($iterationsWithNullSetForExecution > 0) {
    echo 'timeWithNullSetForExecution: '.$timeWithNullSetForExecution."\n";
}
if ($iterationsWithNullUnsetForExecution > 0) {
    echo 'timeWithNullUnsetForExecution: '.$timeWithNullUnsetForExecution."\n";
}

echo "\nFOR SET LOGGER\n";

if ($iterationsWithNullLoggerSetForCreationAndExecution > 0) {
    echo 'timeWithNullLoggerSetForCreationAndExecution: '.$timeWithNullLoggerSetForCreationAndExecution."\n";
}
if ($iterationsWithNullLoggerSetForExecution > 0) {
    echo 'timeWithNullLoggerSetForExecution: '.$timeWithNullLoggerSetForExecution."\n";
}
if ($iterationsWithNullSetForCreationAndExecution > 0) {
    echo 'timeWithNullSetForCreationAndExecution: '.$timeWithNullSetForCreationAndExecution."\n";
}
if ($iterationsWithNullSetForExecution > 0) {
    echo 'timeWithNullSetForExecution: '.$timeWithNullSetForExecution."\n";
}

echo "\n";

if ($iterationsWithNullLoggerSetForCreationAndExecution > 0) {
    echo 'timeWithNullLoggerSetForCreationAndExecution: '.$timeWithNullLoggerSetForCreationAndExecution."\n";
}
if ($iterationsWithNullSetForCreationAndExecution > 0) {
    echo 'timeWithNullSetForCreationAndExecution: '.$timeWithNullSetForCreationAndExecution."\n";
}
if ($iterationsWithNullLoggerSetForExecution > 0) {
    echo 'timeWithNullLoggerSetForExecution: '.$timeWithNullLoggerSetForExecution."\n";
}
if ($iterationsWithNullSetForExecution > 0) {
    echo 'timeWithNullSetForExecution: '.$timeWithNullSetForExecution."\n";
}
