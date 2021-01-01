<?php

namespace C2ePhp\Support;

use Exception;

require_once(dirname(__FILE__) . '/IReader.php');
/// @brief Wrapper class to make reading files easier.

/**
 * Quite often you'll have to make a FileReader in order to use other
 * read from a file. \n
 * This is a simple process, simply pass in the filename to FileReader()
 */
class FileReader implements IReader {

    /// @cond INTERNAL_DOCS

    /** @var resource file handle */
    private $handle;

    /// @endcond

    /**
     * Instantiates a file reader for the given file, ready for reading.
     * @param string $filename A full path to the file you want to open.
     * @throws Exception
     */
    public function __construct($filename) {
        if (!file_exists($filename)) {
            throw new Exception('File does not exist: ' . $filename);
        }
        if (!is_file($filename)) {
            throw new Exception('Target is not a file.');
        }
        if (!is_readable($filename)) {
            throw new Exception('File exists, but is not readable.');
        }

        $this->handle = fopen($filename, 'rb');
    }

    /**
     * Reads a specific number of bytes, returning as string
     * @param int $length
     * @return string
     */
    public function read($length) {
        if ($length > 0) {
            return fread($this->handle, $length);
        }
        return '';
    }

    /**
     * @param int $length
     * @return int
     * @throws Exception
     */
    public function readInt($length) {
        $int = 0;
        for ($x = 0; $x < $length; $x++) {
            $buffer = (ord(fgetc($this->handle)) << ($x * 8));
            if ($buffer === false) {
                throw new Exception('Read failure');
            }
            $int += $buffer;
        }
        return $int;
    }

    /**
     * Gets current read position in file stream
     * @return false|int
     */
    public function getPosition() {
        return ftell($this->handle);
    }

    /**
     * Reads a section of the file stream from a given point
     * @param int $start
     * @param int|false $length
     * @return false|string
     */
    public function getSubString($start, $length = FALSE) {
        $oldPosition = ftell($this->handle);
        fseek($this->handle, $start);
        $data = '';
        if ($length === false) {
            while ($newData = $this->read(4096)) {
                if (strlen($newData) == 0) {
                    break;
                }
                $data .= $newData;
            }
        } else {
            $data = fread($this->handle, $length);
        }
        fseek($this->handle, $oldPosition);
        return $data;
    }

    public function readCString() {
        $string = '';
        while (($char = $this->read(1)) !== false) {
            if (ord($char) == 0) {
                break;
            }
            $string .= $char;
        }
        return $string;
    }

    public function seek($position) {
        fseek($this->handle, $position);
    }

    public function skip($count) {
        fseek($this->handle, $count, SEEK_CUR);
    }
}


