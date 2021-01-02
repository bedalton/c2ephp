<?php

namespace C2ePhp\Support;

/**
 * Class to read from strings in the same way as files
 *
 * This is a convenience class to allow strings to be
 * treated in the same way as files. You won't <i>generally</i>
 * have to create these yourself, but if you want to, go ahead.
 */
class StringReader implements IReader {

    /// @cond INTERNAL_DOCS

    private $position;
    private $string;
    /// @endcond

    /**
     * Creates a new StringReader for the given string.
     * Initialises the position to 0.
     * @param string $string string contents to read from
     */
    public function __construct($string) {
        $this->string = $string;
        $this->position = 0;
    }

    /**
     * Reads a given length of bytes as a string
     * @param int $length
     * @return false|string
     */
    public function read($length) {
        if ($length > 0) {
            if ($this->position+$length > strlen($this->string)) {
                return false;
            }
            $str = substr($this->string, $this->position, $length);

            $this->position += $length;
            return $str;
        }
        return "";
    }

    /**
     * Reads a c-string with self determined length
     * @return false|string
     */
    public function readCString() {
        $string = '';
        while (($char = $this->read(1)) !== false) {
            $string .= $char;
            if ($char == "\0") {
                break;
            }
        }
        return substr($string, 0, -1);
    }

    /**
     * Seeks to a given position in the stream
     * @param $position
     */
    public function seek($position) {
        $this->position = $position;
    }

    /**
     * Skips a given number of bytes in the stream
     * @param $count
     */
    public function skip($count) {
        $this->position += $count;
    }

    /**
     * Gets an int using the given number of bytes in little endian
     * @param int $length
     * @return false|int
     */
    public function readInt($length) {
        return BytesToIntLilEnd($this->read($length));
    }

    /**
     * Gets the current read position in string
     * @return int
     */
    public function getPosition() {
        return $this->position;
    }

    /**
     * Reads a substring of this string reader
     * Reads to end if no length is specified
     * @param int $start
     * @param bool|int $length
     * @return false|string
     */
    public function getSubString($start, $length = FALSE) {
        if ($length == FALSE) {
            $length = strlen($this->string)-$start;
        }
        return substr($this->string, $start, $length);
    }
}

/**
 * Reads bytes into an int in little endian order
 * @param false|string $string
 * @return false|int
 */
function bytesToIntLilEnd($string) { //little endian
    if ($string == "") {
        return false;
    }
    $length = strlen($string);
    $int = 0;
    for ($i = 0; $i < $length; $i++) {
        $int += ord($string{$i}) << ($i*8);
    }
    return $int;
}