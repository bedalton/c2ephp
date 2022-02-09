<?php

namespace C2ePhp\Support;


use Exception;

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

	private $size;
	/// @endcond

	/**
	 * Creates a new StringReader for the given string.
	 * Initialises the position to 0.
	 * @param string $string string contents to read from
	 */
	public function __construct(string $string) {
		$this->string = $string;
		$this->position = 0;
		$this->size = strlen($string);
	}

	/**
	 * Reads a given length of bytes as a string
	 * @param int|null $length length of string to read
	 * @param bool $cpDecode <b>true</b> to decode string from CP-1252 to UTF-8
	 * @param bool $throwing throw exception on not-enough-bytes
	 * @return false|string
	 * @throws Exception
	 */
	public function read(?int $length, bool $cpDecode = FileReader::CP_DECODING_DEFAULT, bool $throwing = TRUE) {
		if (is_null($length)) {
			throw new Exception("Cannot read bytes with null length");
		}
		if ($length > 0) {
			if (($this->position + $length) > $this->size) {
				return NULL;
			}
			$str = substr($this->string, $this->position, $length);
			if ($cpDecode) {
				$str = __ef_decode_reader($str);
			}
			$this->position += $length;
			return $str;
		}
		return NULL;
	}

	/**
	 * Reads a c-string with self determined length
	 * @return string
	 * @throws Exception
	 */
	public function readCString() {
		$string = '';
		while (!is_null($char = $this->read(1, FileReader::CP_DECODING_DEFAULT, FALSE))) {
			if (ord($char) === 0) {
				break;
			}
			$string .= $char;
		}
		return $string;
	}

	/**
	 * Seeks to a given position in the stream
	 * @param int|null $position
	 * @throws Exception
	 */
	public function seek(?int $position) {
		if (is_null($position)) {
			throw new Exception("Cannot seek to null position");
		}
		if ($position > $this->size) {
			throw new Exception('Cannot seek past end of stream');
		}
		$this->position = $position;
	}

	/**
	 * Skips a given number of bytes in the stream
	 * @param int|null $count
	 * @throws Exception
	 */
	public function skip(?int $count) {
		if (is_null($count)) {
			throw new Exception("Cannot skip bytes with null length");
		}
		if ($this->position + $count > $this->size) {
			throw new Exception('Cannot skip bytes past end of stream');
		}
		$this->position += $count;
	}

	/**
	 * Gets an int using the given number of bytes in little endian
	 * @param int|null $length
	 * @return null|int
	 * @throws Exception
	 */
	public function readInt(?int $length) {
		return bytes_to_little_endian($this->read($length, FALSE));
	}

	/**
	 * Gets the current read position in string
	 * @return int
	 */
	public function getPosition() {
		return $this->position;
	}

	/**
	 * Check if there is more data at current position in buffer
	 * @return bool <b>true</b> if there is more data to read at current buffer position
	 */
	public function hasNext() {
		return $this->getPosition() < $this->size;
	}

	/**
	 * Reads a substring of this string reader
	 * Reads to end if no length is specified
	 * @param int|null $start
	 * @param int|null $length
	 * @param bool $safe
	 * @return false|string
	 * @throws Exception
	 */
	public function getSubString(?int $start, ?int $length = NULL, bool $safe = FALSE) {
		if (is_null($start)) {
			throw new Exception("Cannot get substring with null start value");
		}
		if (is_null($length)) {
			return substr($this->string, $start);
		}
		if ($safe) {
			$length = min($this->size - $start, $length);
		}
		return substr($this->string, $start, $length);
	}
}

/**
 * Reads bytes into an int in little endian order
 * @param string|null $string
 * @return int|null
 */
function bytes_to_little_endian(?string $string) { //little endian
	if (is_null($string) || strlen($string) < 1) {
		return NULL;
	}
	$length = strlen($string);
	$int = 0;
	for ($i = 0; $i < $length; $i++) {
		$int += ord($string[$i]) << ($i * 8);
	}
	return $int;
}