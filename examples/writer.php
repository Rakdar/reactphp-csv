<?php

use Rakdar\React\Csv\Writer;
use React\EventLoop\Factory;
use React\EventLoop\Timer\Timer;
use React\Stream\WritableResourceStream;

chdir(__DIR__);
require '../vendor/autoload.php';

// Preperation
$loop = Factory::create();
$writer = new Writer(
    new WritableResourceStream(
        fopen('php://stdout', 'wn'),
        $loop
    )
);
$int = (object)0;

// Adding a timer to periodically print out some data
$loop->addPeriodicTimer(1, function ($timer) use ($writer, $int) {
    /** @var Timer $timer */
    $writer->write([2 * $int->scalar, "fds " . 3 * $int->scalar . " a", 4 * $int->scalar]);
    $int->scalar++;

    // Clean Stopping after printing 10 times
    if ($int->scalar > 10) {
        $writer->close();
        $timer->cancel();
    }
});

$loop->run();