<?php

namespace Rakdar\React\Csv;

use Evenement\EventEmitterInterface;
use Evenement\EventEmitterTrait;
use React\Stream\Util;
use React\Stream\WritableStreamInterface;

/**
 * Object to write csv-data to {@see WritableStreamInterface}.
 *
 * @package Rakdar\React\Csv
 */
class Writer implements EventEmitterInterface
{
    use EventEmitterTrait;

    protected $buffer;

    protected $stream;
    protected $delimiter = ",";
    protected $enclosure = "\"";
    protected $escape = "\\";

    /**
     * Writer constructor.
     * @param WritableStreamInterface $stream
     */
    public function __construct(WritableStreamInterface $stream)
    {
        $this->stream = $stream;

        $this->buffer = fopen("php://memory", "c+");

        Util::forwardEvents($this->stream, $this, ["drain", "error", "close"]);
    }

    /**
     * Writes csv-conform string to underlying stream.
     *
     * @param array $data
     * @return void
     */
    public function write(array $data)
    {
        // Resetting the internal buffer
        ftruncate($this->buffer, 0);
        rewind($this->buffer);

        fputcsv(
            $this->buffer,
            $data,
            $this->delimiter,
            $this->enclosure,
            $this->escape
        );

        rewind($this->buffer);
        $this->stream->write(stream_get_contents($this->buffer));
    }

    /**
     * Closes the underlying stream.
     *
     * @return void
     */
    public function close()
    {
        $this->stream->close();
    }

    /**
     * Sets the delimiter character.
     *
     * @param $delimiter string
     */
    public function setDelimiter($delimiter)
    {
        $this->delimiter = mb_substr($delimiter, 0, 1);
    }

    /**
     * Sets the enclosure character.
     *
     * @param $enclosure string
     */
    public function setEnclosure($enclosure)
    {
        $this->enclosure = mb_substr($enclosure, 0, 1);
    }

    /**
     * Sets the escape character.
     *
     * @param $escape string
     */
    public function setEscape($escape)
    {
        $this->escape = mb_substr($escape, 0, 1);
    }
}
