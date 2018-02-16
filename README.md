# reactphp-csv

## Introduction
Uses PHPs internal `fgetcsv` function to parse data into an array or vice versa to file/stream.

## Installation
```
$ composer require rakdar/reactphp-csv
```

## Read from csv-file
```php
$loop = React\EventLoop\Factory::create();

$inputFd = fopen('country-codes.csv', 'r');
$input = new Rakdar\React\Csv\Reader(
    new React\Stream\ReadableResourceStream($inputFd, $loop)
);

$input->on('data', function ($field) {
    echo $field[10] . PHP_EOL;
});

$loop->run();
```

## Write to csv-file
```php
$loop = React\EventLoop\Factory::create();

$outputFp = fopen('testfile.csv', 'w');
$output = new Rakdar\React\Csv\Writer(
    new React\Stream\WritableResourceStream($outputFp, $loop)
);
$output->write(['Header 1', 'Header 2', 'Header 3']);
$output->write(['Col 1.1', 'Col 1.2', 'Col 1.3']);
$output->write(['Col 2.1', 'Col 2.2', 'Col 2.3']);

$output->close();

$loop->run();
```