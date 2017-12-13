<?php
/**
 * Created by PhpStorm.
 * User: karsten
 * Date: 10.12.17
 * Time: 22:37
 */

namespace Rakdar\React\Csv;


use Evenement\EventEmitterInterface;
use Evenement\EventEmitterTrait;
use React\Stream\ReadableStreamInterface;
use React\Stream\Util;

class Reader implements EventEmitterInterface
{
    use EventEmitterTrait;

    /** @var ReadableStreamInterface $stream */
    private $stream;

    /** @var resource $buffer */
    private $buffer;
    private $paused = false;
    private $delimiter = ";";
    private $enclosure = "\"";
    private $escape = "\\";

    /**
     * Reader constructor.
     * @param $buffer
     */
    public function __construct(ReadableStreamInterface $stream)
    {
        $this->stream = $stream;
        $this->buffer = fopen("php://memory", "c+");

        $this->stream->on("data", [$this, "handleData"]);
        
        Util::forwardEvents($this->stream, $this, ["end", "error", "close"]);
    }

    public function handleData($data)
    {
        fputs($this->buffer, $data);
        $this->parseBuffer();
    }

    public function parseBuffer()
    {
        rewind($this->buffer);
        $start = 0;

        while (
            $this->isPaused() === false &&
            $field = fgetcsv($this->buffer, 0, $this->delimiter, $this->enclosure, $this->escape)
        ) {
            if (
                feof($this->buffer) === false ||
                $this->stream->isReadable() === false
            ) {
                $start = ftell($this->buffer);
                $this->emit("data", [$field]);
            }
        }

        fseek($this->buffer, $start);
        $dataRemainig = stream_get_contents($this->buffer);
        ftruncate($this->buffer, 0);
        fputs($this->buffer, $dataRemainig);
    }

    public function pause()
    {
        $this->stream->pause();
        $this->paused = true;
    }

    public function isPaused()
    {
        return $this->paused;
    }

    public function resume()
    {
        $this->paused = false;
        rewind($this->buffer);
        if (feof($this->buffer) === false) {
            $this->parseBuffer();
        }
        if ($this->isPaused() === false) {
            $this->stream->resume();
        }
    }

    public function close()
    {
        rewind($this->buffer);
        ftruncate($this->buffer, 0);
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