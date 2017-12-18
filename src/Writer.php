<?php

namespace Rakdar\React\Csv;

use React\Stream\Util;
use React\Stream\WritableStreamInterface;

class Writer
{
    protected $buffer;

    protected $stream;
    protected $delimiter = ",";
    protected $enclosure = "\"";
    protected $escape = "\\";


    public function __construct(WritableStreamInterface $stream)
    {
        $this->stream = $stream;

        $this->buffer = fopen("php://memory", "c+");

        Util::forwardEvents($this->stream, $this, ["drain", "error", "close"]);
    }

    public function write(array $data)
    {
        // Resetting the internal buffer
        ftruncate($this->buffer, 0);
        rewind($this->buffer);

        fputcsv(
            $this->buffer,
            $data,
            $this->enclosure,
            $this->delimiter,
            $this->escape
        );

        rewind($this->buffer);
        $this->stream->write(stream_get_contents($this->buffer));
    }

    public function close()
    {
        $this->stream->close();
    }

    public function setDelimiter($delimiter)
    {
        $this->delimiter = mb_substr($delimiter, 0, 1);
    }

    public function setEnclosure($enclosure)
    {
        $this->enclosure = mb_substr($enclosure, 0, 1);
    }

    public function setEscape($escape)
    {
        $this->escape = mb_substr($escape, 0, 1);
    }
}
