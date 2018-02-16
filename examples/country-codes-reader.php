<?php

/*
 * Printing the ISO-4217 Country-Name
 */

use Rakdar\React\Csv\Reader;
use React\EventLoop\Factory;
use React\Stream\ReadableResourceStream;

chdir(__DIR__);
include '../vendor/autoload.php';

$loop = Factory::create();

$start = microtime();
$inputFd = fopen('country-codes.csv', 'r');
$end = microtime();
$input = new Reader(new ReadableResourceStream($inputFd, $loop));
$input->setDelimiter(",");
$input->setParseHeader(false);

$input->on('data', function ($field) {
    echo $field[10] . PHP_EOL;
});

$loop->run();