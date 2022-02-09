<?php

namespace C2ePhp\Support;

/**
 * Interface for reading data.
 *
 * This is implemented by StringReader and FileReader to allow the
 * main c2ephp classes to read data from strings and files with a
 * consistent and simple OO interface. \n
 * IReaders work a bit like file handles: they store a position
 * and all their functions are based around that position.
 */

interface IReader {
    /**
     * Reads an integer
     *
     * @param int $length Length of the int in bytes.
     */
    public function readInt(int $length);

    /**
     * Reads a string
     *
     * @param int|null $length Length of the string to read in bytes
	 * @param bool $cpDecode Decode CP1252 string to UTF-8
	 * @param bool $throwing throw exception on not-enough-bytes
     * @return string|false
     */
    public function read(?int $length, bool $cpDecode = TRUE, bool $throwing = TRUE);

    /**
     * Gets a substring
     *
     * This function is to allow things like PRAYFile to pull out a
     * big chunk of data and then initialise a new StringReader to
     * deal with it. It's not the most efficient way of doing things
     * memory-wise, but it simplifies the code and c2e files don't
     * tend to get big enough to make this inefficiency a concern on
     * any reasonably modern hardware.
     * @param int|null $start
     * @param int|null $length
     * @return string
     */
    public function getSubString(?int $start, ?int $length = NULL);

    /**
     * Gets the position of the cursor
     *
     * This is analogous to ftell in C or PHP.
     * @return integer
     */
    public function getPosition();

    /**
     * Check if there is more data at current position in buffer
     * @return bool <b>true</b> if there is more data to read at current buffer position
     */
    public function hasNext();

    /**
     * Changes the current position in the reader's stream
     *
     * This is analogous to fseek in C or PHP.
     * @param int|null $position
     * @return void
     */
    public function seek(?int $position);

    /**
     * Advances the position of the reader by $count.
     *
     * @param int|null $count
     * @return void
     */
    public function skip(?int $count);

    /**
     * Reads a c-style string at the current position.
     *
     * Read a string of unknown length until the first NUL
     * C-style means that the string is terminated by a NUL (0)
     * character.
     * @return string
     */
    public function readCString();
}

