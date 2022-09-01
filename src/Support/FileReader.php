<?php

namespace C2ePhp\Support;

use Exception;

/**
 * Wrapper class to make reading files easier.
 * Quite often you'll have to make a FileReader in order to use other
 * read from a file. \n
 * This is a simple process, simply pass in the filename to FileReader()
 */
class FileReader implements IReader {

	const CP_DECODING_DEFAULT = FALSE;
    /// @cond INTERNAL_DOCS

    /** @var resource file handle */
    private $handle;

    private $size;

    /// @endcond

    /**
     * Instantiates a file reader for the given file, ready for reading.
     * @param string $filename A full path to the file you want to open.
     * @throws Exception
     */
    public function __construct(string $filename) {
        if (!file_exists($filename)) {
            throw new Exception("File does not exist: $filename");
        }
        if (!is_file($filename)) {
            throw new Exception('Target is not a file.');
        }
        if (!is_readable($filename)) {
            throw new Exception('File exists, but is not readable.');
        }
        $this->size = filesize($filename);
        $this->handle = fopen($filename, 'rb');
    }

	/**
	 * Reads a specific number of bytes, returning as string
	 * @param int|null $length
	 * @param bool $cpDecode decode CP-1252 text to UTF-8
	 * @param bool $throwing throw exception on not-enough-bytes
	 * @return string
	 * @throws Exception
	 */
    public function read(?int $length, bool $cpDecode = self::CP_DECODING_DEFAULT, bool $throwing = TRUE) {
		if (is_null($length)) {
			throw new Exception("Cannot read bytes with null length");
		}
        if ($this->getPosition() + $length > $this->size) {
            if ($throwing) {
                $bytesPast = ($this->size - $this->getPosition());
                throw new Exception("Cannot read past end of file. Expected: $length bytes. Found: $bytesPast");
            }
            return NULL;
        }
		$str = $length ? fread($this->handle, $length) : '';
		if ($str && $cpDecode) {
			$str = __ef_decode_reader($str);
		}
        return $str;
    }

    /**
     * @param int|null $length
     * @return int
     * @throws Exception
     */
    public function readInt(?int $length) {
		if (is_null($length)) {
			throw new Exception("Cannot read int with null request byte length");
		}
        $int = 0;
        if (($this->getPosition() + $length) > $this->size) {
            throw new Exception("Cannot read int($length) past end of buffer");
        }
        for ($x = 0; $x < $length; $x++) {
            $buffer = fgetc($this->handle);
            if ($buffer === FALSE)
                throw new Exception("Cannot read int($length); Read failure");
            $buffer = (ord($buffer) << ($x * 8));
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
     * Check if there is more data at current position in buffer
     * @return bool <b>true</b> if there is more data to read at current buffer position
     */
    public function hasNext() {
        return $this->getPosition() < $this->size;
    }

    /**
     * Reads a section of the file stream from a given point
     * @param int|null $start
     * @param int|null $length
     * @return false|string
     * @throws Exception
     */
    public function getSubString(?int $start, ?int $length = NULL) {
		if (is_null($start)) {
			throw new Exception("Cannot get substring with null start position");
		}
        $oldPosition = ftell($this->handle);
        fseek($this->handle, $start);
        if (is_null($length)) {
            $length = $this->size - $start;
        }
        $data = fread($this->handle, $length);
        fseek($this->handle, $oldPosition);
        return $data;
    }

    /**
     * @throws Exception
     */
    public function readCString() {
        $string = '';
        while (!is_null($char = $this->read(1, self::CP_DECODING_DEFAULT, FALSE))) {
            if (ord($char) === 0) {
                break;
            }
            $string .= $char;
        }
        return $string;
    }

	/**
	 * Changes the current position in the reader's stream
	 *
	 * This is analogous to fseek in C or PHP.
	 * @param int|null $position
	 * @return void
	 * @throws Exception
	 */
    public function seek(?int $position) {
		if (is_null($position)) {
			throw new Exception("Cannot seek to null position");
		}
        fseek($this->handle, $position);
    }

	/**
	 * Advances the position of the reader by $count.
	 *
	 * @param int|null $count
	 * @return void
	 * @throws Exception
	 */
    public function skip(?int $count) {
		if (is_null($count)) {
			throw new Exception("Cannot skip bytes with null length");
		}
        fseek($this->handle, $count, SEEK_CUR);
    }

    public function close() {
        if (is_resource($this->handle))
            fclose($this->handle);
    }
}


