<?php

use Rakdar\React\Csv\Writer;
use React\EventLoop\Factory;
use React\EventLoop\Timer\Timer;
use React\Stream\WritableResourceStream;

require '../vendor/autoload.php';

$loop = Factory::create();

$writer = new Writer(new WritableResourceStream(fopen('php://stdout', 'w'), $loop));
$int = (object)0;

$loop->addPeriodicTimer(1, function ($timer) use ($writer, $int) {
    /** @var Timer $timer */
    $writer->write([2 * $int->scalar, "fds " . 3 * $int->scalar . " a", 4 * $int->scalar]);
    $int->scalar++;
    if ($int->scalar > 10) {
        $writer->close();
        $timer->cancel();
    }
});

$loop->run();