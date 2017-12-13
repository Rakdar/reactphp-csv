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
$input = new Reader(new ReadableResourceStream(fopen('country-codes.csv', 'r'), $loop));
$input->setDelimiter(",");

$input->on('data', function ($field) {
    echo $field[10] . PHP_EOL;
});

$loop->run();