<?php

namespace C2ePhp\Agents\PRAY;

use Exception;

require_once(dirname(__FILE__) . '/PrayBlock.php');

/**
 * Class for a FILE block.
 *
 * Used mainly in agent files, a FILE block can contain any type of data.
 * These blocks are primarily used for sprite, body data and genetics files.
 * You can only tell which type of file it is by looking at the file's name.
 */
class FILEBlock extends PrayBlock {

    /**
     * Creates a new FILEBlock
     *
     * FILEBlocks are currently read-only. \n
     * If $prayFile is not null, all the data about this block
     * will be read from the PRAYFile.
     * @param PRAYFile|null $prayFile The PRAYFile this FILEBlock belongs to. Can
     * be null.
     * @param string $name The name of this file block (also the file's name)
     * @param string $content The binary data of this file block.
     * @param int $flags The block's flags. See PrayBlock.
	 * @param string|null $blockTag an override for the FILE tag, as it can be any 4 letter code in practice
     * @throws Exception
     */
    public function __construct(?PRAYFile $prayFile, string $name, string $content, int $flags, ?string $blockTag = null) {
        parent::__construct($prayFile, $name, $content, $flags, $blockTag ?? PRAY_BLOCK_FILE);
    }

    /// @cond INTERNAL_DOCS

    /**
     * Compiles the block's data
     *
     * This function is for compatibility with PrayBlock's abstract
     * class and allows FILEBlocks to be "compiled" and "decompiled"
     * @throws Exception
     */
    protected function compileBlockData() {
        return $this->getData();
    }

    /**
     * Decompiles the block's data
     *
     * In other blocks, this function would convert the binary data
     * from the block into useful data. In FILEBlocks, this is
     * impractical because the binary data could be in any c2e file
     * format.
     *
     * @throws Exception
     */
    protected function decompileBlockData() {
        throw new Exception('It\'s impossible to decode a FILE.');
    }

    /// @endcond

    /**
     * Gets the name of the file
     *
     * @return string
     */
    public function getFileName() {
        return $this->getName();
    }

    /**
     * Gets the contents of the file
     *
     * @return string
     * @throws Exception
     */
    public function getFileContents() {
        return $this->getData();
    }
}
